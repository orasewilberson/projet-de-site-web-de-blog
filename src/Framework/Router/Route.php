<?php
namespace Framework\Router;

use Psr\Http\Server\MiddlewareInterface;

class Route
{
    private $name;
    private $params;
    private $callback;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string|callable $callable
     * @param array $params
     */
    public function __construct(string $name, $callable, array $params)
    {
        $this->name = $name;
        $this->callback = $callable; // Correction ici
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Undocumented function
     * @return string|Callable
     */ 
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set the value of callback
     *
     * @return  self
     */ 
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }
}
