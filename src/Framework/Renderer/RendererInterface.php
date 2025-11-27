<?php

namespace Framework\Renderer;

interface RendererInterface
{

        /**
     * permet de rajouter un chemin pour charger le vue
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

    
    /**
     * permet de rendre une vue
     * le chemin peut etre precise avec des namespace rajoutes via addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;
   
    
    /**
     * permet de rajouter des variables globales a toutes les vues
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void;
}
