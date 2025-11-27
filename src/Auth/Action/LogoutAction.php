<?php
namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use App\Blog\Session\FlashService;
use Framework\Response\RedirectResponse;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction {
    
    private $renderer;

    private $auth;

    private $flashService;

    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $flashService)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $this->auth->logout();
        $this->flashService->succes('Vous etes maintenant deconnecte');
        return new RedirectResponse('/');
    }
}