<?php
namespace Framework;

use AltoRouter;
use Framework\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * Undocumented variable
     *
     * @var AltoRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }
    
    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, $callable, string $name)
    {
        $this->router->map('GET', $path, $callable, $name);
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function post(string $path, $callable, ?string $name=null)
    {
        $this->router->map('POST', $path, $callable, $name);
    }
     

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function delete(string $path, $callable, ?string $name=null)
    {
        $this->router->map('DELETE', $path, $callable, $name);
    }

    /**
     * Undocumented function
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        //var_dump($request); die();
        $match = $this->router->match($request->getUri()->getPath());
        if ($match) {
            return new Route($match['name'], $match['target'], $match['params']);
        }

        return null;
    }

    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generate($name, $params);
        if(!empty($queryParams)){
            return $uri . '?' . \http_build_query($queryParams);
        }
        return $uri;
    }
}
