<?php
namespace App\Auth;

use Framework\Auth;
use Framework\Auth\User;
use App\Auth\DatabaseAuth;
use App\Auth\AuthTwigExtension;
use App\Auth\ForbiddenMiddleware;
use App\Blog\Session\SessionInterface;
return [
    'auth.login' => '/login',
    'twig.extensions' => \DI\add([
        \DI\get(AuthTwigExtension::class)
    ]),
    User::class => \DI\factory(function(Auth $auth) {
       return $auth->getUser(); 
    })->parameter('auth', \DI\get(Auth::class)),
    Auth::class => \DI\get(DatabaseAuth::class),
    ForbiddenMiddleware::class => \DI\create()
        ->constructor(\DI\get('auth.login'), \DI\get(SessionInterface::class))
];