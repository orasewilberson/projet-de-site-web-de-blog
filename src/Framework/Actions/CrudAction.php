<?php
namespace Framework\Actions;

use Framework\Router;
use Framework\Validator;
use Framework\Database\Table;
use Framework\Database\Hydrator;
use App\Blog\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction {
    
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    protected $messages = [
        'create' => "L'element a bien ete cree",
        'edit' => "L'element a bien ete modifie"
    ];

    use RouterAwareAction;
    
    public function __construct(RendererInterface $renderer, Router $router, Table $table, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if($request->getMethod() === 'DELETE'){
            return $this->delete($request);
        }
        if(substr((string)$request->getUri(), -3) === 'new'){
            return $this->create($request);
        }
        if($request->getAttribute('id')){
            return $this->edit($request);
        }
            return $this->index($request);
    }

    /**
     * Affiche la liste des elements
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findAll()->paginate(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Edit un element
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request) 
    {
    
        $errors = [];
        $item = $this->table->find($request->getAttribute('id'));
        
        if($request->getMethod() === 'POST'){
           //$params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
            $validator = $this->getValidator($request);
           if($validator->isValid()) {
           $this->table->update($item->id, $this->getParams($request, $item));
           $this->flash->succes($this->messages['edit']);
           return $this->redirect($this->routePrefix . '.index');
           }
           $errors = $validator->getErrors();
           Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath . '/edit', 
            $this->formParams(compact('item', 'errors')));
    }
    
    /**
     * creer un nouvel element
     * @return void
     */
    public function create(Request $request)
    {
        $item = null;
        $errors = [];

        $item = $this->getNewEntity();
        if($request->getMethod() === 'POST'){
            $validator = $this->getValidator($request);
            if($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->succes($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors(); 
         }
       
        return $this->renderer->render(
            $this->viewPath . '/create', 
            $this->formParams(compact('item', 'errors'))); 
     }

    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Genere le validateur pour valider les donnees
     *
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function($key) {
            return in_array($key, []);
           }, ARRAY_FILTER_USE_KEY);
        
    }

    protected function getValidator(Request $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));

    }

    /**
     * Genere une nouvelle entite pour l'action de creation
     *
     * @return void
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * permet de traiter les parametre a envoyer a la vue
     *
     * @param [type] $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }


}