<?php
namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware {

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        //verifier si le dernier caractere passer dans l'url est un slash / si ui redigerer vers la bonne version sans le /
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            // Avoid redirect loops with Apache directory-slash behavior:
            // if the requested path without trailing slash maps to an existing directory
            // on the filesystem (for example the `public` folder), let the request pass
            // to the next middleware instead of issuing a redirect.
            $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], "\\/") : null;
            if ($docRoot) {
                $targetPath = $docRoot . DIRECTORY_SEPARATOR . ltrim(substr($uri, 1), '/\\');
                if (is_dir($targetPath)) {
                    return $next($request);
                }
            }
            $response = new Response(301, ['Location' => substr($uri, 0, -1)]);
            return $response;
        }
        return $next($request);

    }
}