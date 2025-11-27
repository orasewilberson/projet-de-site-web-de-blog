<?php

namespace App\Blog;


use Framework\Module;
use Framework\Router;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Actions\PostIndexAction;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use Framework\Renderer\RendererInterface;


class BlogModule extends Module{
    
    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');
        $router->get($container->get('blog.prefix') . '/[*:slug]-[i:id]', PostShowAction::class, 'blog.show');
        $router->get($container->get('blog.prefix') . '/category/[*:slug]', CategoryShowAction::class, 'blog.category');


        if($container->has('admin.prefix')){
            $prefix = $container->get('admin.prefix');
            $router->get("$prefix/posts", PostCrudAction::class, 'blog.admin.index');
            
            $router->get("$prefix/new", PostCrudAction::class, 'blog.admin.create');
            $router->post("$prefix/new", PostCrudAction::class, '');

            $router->get("$prefix/posts/[i:id]", PostCrudAction::class, 'blog.admin.edit');
            $router->post("$prefix/posts/[i:id]", PostCrudAction::class, 'blog.admin.post');
           
            $router->delete("$prefix/posts/[i:id]", PostCrudAction::class, 'blog.admin.delete');

            //category
            $router->get("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin.index');

            $router->get("$prefix/categories/new", CategoryCrudAction::class, 'blog.category.admin.create');
            $router->post("$prefix/categories/new", CategoryCrudAction::class, '');
            
            $router->get("$prefix/categories/[i:id]", CategoryCrudAction::class, 'blog.category.admin.edit');
            $router->post("$prefix/categories/[i:id]", CategoryCrudAction::class, 'blog.category.admin.post');

            $router->delete("$prefix/categories/[i:id]", CategoryCrudAction::class, 'blog.category.admin.delete');




            
            
        }
    }

    
}