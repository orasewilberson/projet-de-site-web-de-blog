<?php
namespace App\Blog;

use App\Blog\BlogModule;
use App\Blog\BlogWidget;
use function \DI\{autowire, get};

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => \DI\add([get(BlogWidget::class)])
];