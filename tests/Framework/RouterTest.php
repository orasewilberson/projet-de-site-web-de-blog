<?php
namespace tests\Framework;

use Framework\Router;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;

class RouterTest extends TestCase{

    /**
     * Undocumented variable
     *
     * @var Route
     */
    private $router;

    public function setup(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function() { return 'hello'; }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallBack(), [$request]));
    }

    public function testGetMethodIfUrlDoesNotExit()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogaze', function() { return 'hello'; }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParamaters()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->get('/blog', function() { return 'azeaze'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function() { return 'hello'; }, 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallBack(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
        
        //Test invalid url
        $route = $this->router->match(New ServerRequest('GET', '/blog/mon_slug-18'));
        $this->assertEquals(null, $route); 
    }

    public function testGenerateUri()
  {
    $this->router->get('/blog', function() { return 'azeaze'; }, 'posts');
    $this->router->get('/blog/[*:slug]-[i:id]', function() { return 'hello'; }, 'post.show');
    $uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => 18]);
    $this->assertEquals('/blog/mon-article-18', $uri);
  }
  

    public function testGenerateUriWithQueryParams()
    {
        $this->router->get('/blog', function() { return 'azeaze'; }, 'posts');
        $this->router->get('/blog/[*:slug]-[i:id]', function() { return 'hello'; }, 'post.show');
        $uri = $this->router->generateUri(
            'post.show', 
            ['slug' => 'mon-article', 'id' => 18],
            ['p' => 2]    
        );
        $this->assertEquals('/blog/mon-article-18?p=2', $uri);
    }

}
