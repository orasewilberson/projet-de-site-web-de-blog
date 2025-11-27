<?php
namespace tests\Framework\Modules;

use Framework\Router;

class ErrordModule {

    public function __construct(Router $router)
    {
       $router->get('/demo', function(){ 
        return new \stdClass(); 
    }, 'demo'); 
    }
}