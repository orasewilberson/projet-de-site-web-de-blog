<?php
namespace App\Blog\Actions;

use PDO;
use Framework\Router;
use App\Blog\Table\PostTable;
use GuzzleHttp\Psr7\Response;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var Router
     */
    private $router;

    use RouterAwareAction;
    
    public function __construct(RendererInterface $renderer, 
    Router $router, 
    PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }
    
    /**
     * Afficher un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {   
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));
        if($post->slug !== $slug){
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return  $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}