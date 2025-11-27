<?php


use Framework\Router;
use App\Blog\Session\PHPSession;
use Framework\Twig\CsrfExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Framework\Twig\FlashExtension;
use Psr\Container\ContainerInterface;
use App\Blog\Session\SessionInterface;
use Framework\Twig\PagerFantaExtension;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Router\RouterTwigExtension;
use Framework\Renderer\TwigRendererFactory;

return [
    'env' => \DI\env('ENV', 'production'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'mossupersite',
    'database.port' => 3300,
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
        \DI\get(CsrfExtension::class)

    ],
    SessionInterface::class => \DI\create(PHPSession::class),
    CsrfMiddleware::class => \DI\create()->constructor(\DI\get(SessionInterface::class)),
    Router::class => \DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    \PDO::class => function (ContainerInterface $c){
    return new PDO(
        'mysql:host=' . $c->get('database.host') . ';port=' . $c->get('database.port') . ';dbname=' . $c->get('database.name'),
        $c->get('database.username'),
        $c->get('database.password'),
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
        );
    }
];