<?php
namespace App\Auth;

use Framework\Module;
use Framework\Router;
use App\Auth\Action\LoginAction;
use App\Auth\Action\LogoutAction;
use Psr\Container\ContainerInterface;
use App\Auth\Action\LoginAttemptAction;
use Framework\Renderer\RendererInterface;


class AuthModule Extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('auth', __DIR__ . '/views');
        $router->get($container->get('auth.login'), LoginAction::class, 'auth.login');   
        $router->post($container->get('auth.login'), LoginAttemptAction::class, '');   
        $router->post('/logout', LogoutAction::class, 'auth.logout');   
    }
}
