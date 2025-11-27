<?php
namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    private $twig;
    
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * permet de rajouter un chemin pour charger le vue
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    
    /**
     * permet de rendre une vue
     * le chemin peut etre precise avec des namespace rajoutes via addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }
    
    /**
     * permet de rajouter des variables globales a toutes les vues
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Get the value of twig
     */ 
    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
