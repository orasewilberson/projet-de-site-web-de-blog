<?php
namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\PostUpload;
use Framework\Validator;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use App\Blog\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;


class PostCrudAction extends CrudAction {

    protected $viewPath = "@blog/admin/posts";

    protected $routePrefix = "blog.admin";

    private $categoryTable;

    private $postUpload;

    public function __construct(
        RendererInterface $renderer, 
        Router $router, PostTable $table, 
        FlashService $flash, CategoryTable $categoryTable,
        PostUpload $postUpload)
    {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    public function delete(Request $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    protected function formParams(array $params): array
    {
       $params['categories'] = $this->categoryTable->findList();
       $params['categories']['1231123121'] = 'categorie fake';
       return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime(); 
        return $post;
    }
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Post $post
     * @return array
     */
    protected function getParams(Request $request, $post): array{
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        //uploader le fichier
        $image = $this->postUpload->upload($params['image'], $post->image);
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params = array_filter($params, function($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published']);
           }, ARRAY_FILTER_USE_KEY);
           return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }

    protected function getValidator(Request $request)
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->dateTime('created_at')
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');
        if(is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}