<?php
namespace Tests\App\Blog\Actions;

use PDO;
use Framework\Router;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use App\Blog\Actions\PostShowAction;
use Framework\Renderer\RendererInterface;


class PostShowActionTest extends TestCase {
    
    /**
     * @var BlogAction
     */
    private $action;
    private $renderer;
    private $postTable; 
    private $router;

    public function setUp(): void
    {
       $this->renderer = $this->createMock(RendererInterface::class);
       $this->postTable = $this->createMock(PostTable::class);
        
        $this->router = $this->createMock(Router::class);
        $this->action = new PostShowAction(
            $this->renderer,
            $this->router,
            $this->postTable
        );
    }

    public function makePost(int $id, string $slug): Post{
         // Article
         $post = new Post();
         $post->id = $id;
         $post->slug = $slug;
         return $post;
    }

    public function testShowRedirect() {
        // Création d'un objet Post fictif
        $post = $this->makePost(9, 'azezae-azezae');
        
        // Création d'une requête
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
    
        // Vérification que $this->router est initialisé
        $this->assertNotNull($this->router);
    
        // Utilisation de $this->router pour simuler le comportement de la méthode generateUri()
        $this->router->expects($this->once())
            ->method('generateUri')
            ->with('blog.show', ['id' => $post->id, 'slug' => $post->slug])
            ->willReturn('/demo2');
    
        // Utilisation de $this->postTable pour simuler le comportement de la méthode find()
        $this->postTable->expects($this->once())
            ->method('findWithCategory')
            ->with($post->id)
            ->willReturn($post);
    
        // Appel de la méthode à tester
        $response = call_user_func_array($this->action, [$request]);
    
        // Assertions sur la réponse
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/demo2', $response->getHeaderLine('Location'));
    }
    

    public function testShowRender() {
        // Création d'un objet Post fictif
        $post = $this->makePost(9, 'azezae-azezae');
    
        // Création d'une requête
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
    
        // Vérification que $this->router est initialisé
        $this->assertNotNull($this->router);
    
        // Utilisation de $this->postTable pour simuler le comportement de la méthode find()
        $this->postTable->expects($this->once())
            ->method('findWithCategory')
            ->with($post->id)
            ->willReturn($post);
    
        // Vérification que $this->renderer est initialisé
        $this->assertNotNull($this->renderer);
    
        // Utilisation de $this->renderer pour simuler le comportement de la méthode render()
        $this->renderer->expects($this->once())
            ->method('render')
            ->with('@blog/show', ['post' => $post])
            ->willReturn('');
    
        // Appel de la méthode à tester
        $response = call_user_func_array($this->action, [$request]);
    
        // Assertion pour vérifier que la méthode a été appelée
        $this->assertEquals(true, true);
    }
    
}
