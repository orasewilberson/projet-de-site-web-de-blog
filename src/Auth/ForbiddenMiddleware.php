<?php
namespace App\Auth;

use Framework\Auth\User;
use App\Blog\Session\FlashService;
use App\Blog\Session\SessionInterface;
use Framework\Auth\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Framework\Response\RedirectResponse;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForbiddenMiddleware implements MiddlewareInterface {

    private $loginPath; 

    private $session;

    public function __construct(string $loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
       try {
        return $handler->handle($request);
       } catch (ForbiddenException $ex) {
            return $this->redirectLogin($request);
       } catch(\TypeError $error) {
            if(\strpos($error->getMessage(), User::class) !== false) {
              return $this->redirectLogin($request);  
            }
       }
    }

    public function redirectLogin(ServerRequestInterface $request): ResponseInterface 
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vous devez posseder un compte pour acceder a cette page');
        return new RedirectResponse($this->loginPath);
    }
}