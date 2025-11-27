<?php
namespace Framework\Renderer;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Framework\Renderer\TwigRenderer;
use Psr\Container\ContainerInterface;


class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container) :TwigRenderer
    {
        $debug = $container->get('env') !== 'production';
        $viewPath = $container->get('views.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new \Twig\Environment($loader, [
            'debug' => $debug,
            'cache' => $debug ? false : 'tmp/views',
            'auto_reload' => $debug
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        
        if($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);   
            }
        }
        return new TwigRenderer($twig);
    }
}