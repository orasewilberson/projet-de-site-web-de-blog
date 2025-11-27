<?php
namespace App\Auth\Action;

use Framework\Router;
use App\Auth\DatabaseAuth;
use App\Blog\Session\FlashService;
use App\Blog\Session\SessionInterface;
use Framework\Actions\RouterAwareAction;
use Framework\Response\RedirectResponse;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction {
    
    private $renderer;

    private $auth;

    private $router;

    private $session;

    use RouterAwareAction;
    
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, Router $router, SessionInterface $session)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
           (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
           $this->redirect('auth.login'); 
        }
    }

}