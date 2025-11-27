<?php

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

use Framework\App;
use Twig\Environment;
use Middlewares\Whoops;
use App\Auth\AuthModule;
use App\Blog\BlogModule;
use App\Admin\AdminModule;
use App\Auth\ForbiddenMiddleware;
use function  Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\TwigRenderer;
use Framework\Auth\LoggedInMiddleware;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;


$app = (new App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AuthModule::class);
$container = $app->getContainer();    
$app->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe($container->get('admin.prefix'), LoggedInMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);



if(php_sapi_name() !== 'cli'){
    $response = $app->run(ServerRequest::fromGlobals());
    send($response); 
}
