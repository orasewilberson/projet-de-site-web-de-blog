<?php
namespace tests\Framework;

use PHPUnit\Framework\TestCase;
use Framework\Renderer\PHPRenderer;

class PHPRendererTest extends TestCase {
    
    private $renderer;

    public function setUp(): void
    {
        $this->renderer = new PHPRenderer(__DIR__ . '/views');
    }

    public function testRenderTheRightPath() {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('salut les gens', $content);
    } 

    public function testRenderDefaultPath() {
        $content = $this->renderer->render('demo');
        $this->assertEquals('salut les gens', $content);
    } 

    public function testRenderWithParams() {
        $content = $this->renderer->render('demoparams', ['nom' => 'Marc']);
        $this->assertEquals('salut Marc', $content);
    } 

    public function testGlobalParamaters() {
        $this->renderer->addGlobal('nom', 'Marc');
        $content = $this->renderer->render('demoparams');
        $this->assertEquals('salut Marc', $content);
    } 
}
