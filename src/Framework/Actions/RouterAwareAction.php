<?php
namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Rajoute des methodes liee a l'utilisation du Router
 * 
 * Trait RouterAwareAction
 * @package Framework\Actions
 */
trait RouterAwareAction {

    /**
     * Renvoie une reponse de redirection
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}