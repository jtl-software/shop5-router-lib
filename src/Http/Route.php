<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http;

use Izzle\Translation\Services\Translation;
use JTL\Plugin\PluginInterface;
use JTL\Shop5Router\Exceptions\InvalidControllerException;
use JTL\Shop5Router\Http\Controller\AbstractController;
use JTL\Shop5Router\Traits\Translatable;
use function explode;
use InvalidArgumentException;
use Izzle\Support\Str;
use JTL\Shop5Router\Traits\Shopable;
use JTL\Shop5Router\Traits\Pluginable;
use Symfony\Component\HttpFoundation\Request;
use Shop;
use function sprintf;
use function strpos;
use function ucfirst;

/**
 * Class Route
 * @package JTL\Shop5Router\Http
 */
class Route
{
    use Shopable, Pluginable, Translatable;
    
    /**
     * @var string
     */
    protected string $controllerPath;
    
    /**
     * @var string
     */
    protected string $action;
    
    /**
     * @var string
     */
    protected string $controller;
    
    /**
     * @var string
     */
    protected string $method;
    
    /**
     * @var array
     */
    protected array $arguments = [];
    
    /**
     * @var AbstractController[]
     */
    protected array $controllers = [];
    
    /**
     * @param string $controllerPath
     * @param string $action
     * @param Shop $shop
     * @param PluginInterface $plugin
     * @param Translation|null $translator
     * @param array $arguments
     */
    public function __construct(
        string $controllerPath,
        string $action,
        Shop $shop,
        PluginInterface $plugin,
        ?Translation $translator = null,
        array $arguments = []
    ) {
        if (strpos($action, '.') === false) {
            throw new InvalidArgumentException('Invalid Action. Syntax: <controller>.<method>');
        }
        
        $params = explode('.', $action);
        
        if (count($params) !== 2) {
            throw new InvalidArgumentException('Invalid Action. Syntax: <controller>.<method>');
        }
        
        if (isset($arguments['action'])) {
            unset($arguments['action']);
        }
        
        $this->setControllerPath($controllerPath);
        $this->setAction($action);
        $this->setController($params[0]);
        $this->setMethod($params[1]);
        $this->setArguments($arguments);
        $this->setShop($shop);
        $this->setPlugin($plugin);
        
        if ($translator !== null) {
            $this->setTranslator($translator);
        }
    }
    
    /**
     * @param Request $request
     * @throws InvalidControllerException
     * @return mixed
     */
    public function call(Request $request)
    {
        if (empty($this->controllers[$this->getController()])) {
            $fqn = sprintf(
                '%s\%sController',
                $this->getControllerPath(),
                ucfirst(Str::camel($this->getController()))
            );
            
            $controller = new $fqn($this->getShop(), $this->getPlugin(), $this->getTranslator());
            
            if (!($controller instanceof AbstractController)) {
                throw new InvalidControllerException(sprintf(
                    'Controller must be an instance of \'%s\'. \'%s\' given.',
                    AbstractController::class,
                    get_class($controller)
                ));
            }
            
            $this->controllers[$this->getController()] = $controller;
        }
        
        return $this->controllers[$this->getController()]->call($request, $this->getMethod(), $this->getArguments());
    }
    
    /**
     * @return string
     */
    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }
    
    /**
     * @param string $controllerPath
     *
     * @return self
     */
    public function setControllerPath(string $controllerPath): self
    {
        $this->controllerPath = $controllerPath;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
    
    /**
     * @param string $action
     *
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }
    
    /**
     * @param string $controller
     *
     * @return self
     */
    public function setController(string $controller): self
    {
        $this->controller = $controller;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * @param string $method
     *
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = Str::camel($method);
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
    
    /**
     * @param array $arguments
     *
     * @return self
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }
    
    /**
     * @param array $controllers
     *
     * @return self
     */
    public function setControllers(array $controllers): self
    {
        $this->controllers = $controllers;
        
        return $this;
    }
}
