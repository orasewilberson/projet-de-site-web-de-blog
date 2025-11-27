<?php

namespace tests\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Middleware\CsrfMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Framework\Exception\CsrfInvalidException;

class CsrfMiddlewareTest extends TestCase
{
    private $middleware;
    private $session;

    public function setUp(): void
    {
        $this->session = ['csrf' => []]; // Initialisation avec un tableau vide pour 'csrf'
       // $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session, 50);
    }

    public function testLetGetRequestPass() {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new ServerRequest('GET', '/demo');
        $this->middleware->process($request, $handler);
    }

    public function testBlockPostRequestWithoutCsrf() {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testBlockPostRequestWithInvalidCsrf() {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'invalid_token']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testLetPostWithTokenPass() {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new ServerRequest('POST', '/demo');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
    }  

    public function testLetPostWithTokenPassOnce() {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = new ServerRequest('POST', '/demo');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }  
    
    public function testLimitTheTokenNumber() {
        for ($i = 0; $i < 100; $i++) { 
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}
