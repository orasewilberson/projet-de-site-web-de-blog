<?php
namespace Framework\Twig;

use Framework\Router;
use Twig\TwigFunction;
use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;
use Pagerfanta\View\TwitterBootstrap4View;

class PagerFantaExtension extends AbstractExtension {
    
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Genere la pagination
     *
     * @param Pagerfanta $paginatedResults
     * @param string $route
     * @param array $routerParams
     * @param array $queryArgs
     * @return string
     */
    public function paginate(Pagerfanta $paginatedResults, string $route, array $routerParams = [], array $queryArgs = []): string
    {
        $view = new TwitterBootstrap4View();
        return $view->render($paginatedResults, function (int $page) use ($route, $routerParams, $queryArgs) {
            if($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateUri($route, $routerParams, $queryArgs);
        });
    }
}