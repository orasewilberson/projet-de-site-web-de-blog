<?php
namespace Framework;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Framework\Middleware\RoutePrefixedMiddleware;

class App implements RequestHandlerInterface
{
    /**
     * list of modules
     *
     * @var array
     */
    private $modules = [];

    private $definition;

    /**
     * @var ContenairInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middlewares;

    private $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Rajoute un module au niveau de l'application
     *
     * @param string $module
     * @return self
     */
    public function addModule(string $module): self 
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Ajout un middleware
     *
     * @param string $routePrefix
     * @param string|null $middleware
     * @return self
     */
    public function pipe(string $routePrefix, ?string $middleware = null):self
    {
        if($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware  = $this->getMiddleware();
        if(\is_null($middleware)){
            throw new \Exception('Aucun middleware n\'a intercepte cette requette');
        } elseif(\is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
        
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if($this->container === null) {
            $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions($this->definition);
        foreach ($this->modules as $module) {
            if($module::DEFINITIONS){
                $builder->addDefinitions($module::DEFINITIONS);
            }
        }
        $this->container = $builder->build();
        }
    return $this->container;
    }

    /**
     * @return object
     */
    private function getMiddleware()
    {
        if(\array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->process($request);
    }

    /**
     * Get list of modules
     *
     * @return  array
     */ 
    public function getModules()
    {
        return $this->modules;
    }
}
