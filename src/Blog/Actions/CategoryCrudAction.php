<?php
namespace App\Blog\Actions;

use Framework\Router;
use Framework\Validator;
use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use App\Blog\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request; 


class CategoryCrudAction extends CrudAction {

    protected $viewPath = "@blog/admin/categories";

    protected $routePrefix = "blog.category.admin";

    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }
    
    protected function getParams(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function($key) {
            return in_array($key, ['name', 'slug']);
           }, ARRAY_FILTER_USE_KEY);
          
    }

    protected function getValidator(Request $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->slug('slug');

    }
}