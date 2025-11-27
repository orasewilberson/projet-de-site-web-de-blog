<?php
namespace tests\Framework\Middleware;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Middleware\MethodMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class MethodMiddlewareTest extends TestCase
{
    private $middleware;

    public function setUp(): void
    {
        $this->middleware = new MethodMiddleware();
    }

    public function testAddMethod() {
        $request = (new ServerRequest('POST', '/demo'))
            ->withParsedBody(['_method' => 'DELETE']);
            \call_user_func_array($this->middleware, [$request, function (ServerRequestInterface $request) {
                $this->assertEquals('DELETE', $request->getMethod());
            }]);
    }
}

