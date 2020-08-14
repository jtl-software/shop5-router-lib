<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http;

use JTL\Plugin\PluginInterface;
use JTL\Shop5Router\Http\Error\ErrorTranslator;
use JTL\Shop5Router\Traits\ErrorTranslatable;
use Throwable;
use function json_decode;
use function json_encode;
use JTL\Shop5Router\Traits\Shopable;
use JTL\Shop5Router\Traits\Pluginable;
use Shop;
use JsonException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 * @package JTL\Shop5Router\Http
 */
class Router
{
    use Shopable, Pluginable, ErrorTranslatable;
    
    /**
     * @var string
     */
    protected string $controllerPath;
    
    /**
     * @var Route
     */
    protected Route $route;
    
    /**
     * @var array
     */
    protected array $beforeCallbacks = [];
    
    /**
     * @var array
     */
    protected array $afterCallbacks = [];
    
    /**
     * @param string $controllerPath
     * @param Shop $shop
     * @param PluginInterface $plugin
     * @param ErrorTranslator|null $errorTranslator
     */
    public function __construct(
        string $controllerPath,
        Shop $shop,
        PluginInterface $plugin,
        ?ErrorTranslator $errorTranslator = null) {
        $this->setControllerPath($controllerPath);
        $this->setShop($shop);
        $this->setPlugin($plugin);
        
        if ($errorTranslator !== null) {
            $this->setErrorTranslator($errorTranslator);
        }
    }
    
    /**
     * @throws JsonException
     * @return string
     */
    public function send(): string
    {
        $request = Request::createFromGlobals();
        $arguments = $request->getMethod() === Request::METHOD_POST ?
            json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR) : $request->query->all();
        
        $this->route = new Route(
            $this->getControllerPath(),
            $arguments['action'] ?? '',
            $this->getShop(),
            $this->getPlugin(),
            $arguments
        );
        
        $response = [
            'code' => 0,
            'data' => null,
            'error' => null
        ];
        
        try {
            $this->callBeforeCallbacks($request, $arguments);
        } catch (Throwable $e) { }
        
        try {
            $response['data'] = $this->callAfterCallbacks($request, $arguments, $this->route->call($request));
        } catch (Throwable $e) {
            $response['data'] = null;
            $response['code'] = $e->getCode();
            $response['error'] = ($this->errorTranslator() !== null && $this->errorTranslator()->has($e->getCode()))
                ? $this->errorTranslator()->get($e->getCode()) : $e->getMessage();
        }
    
        $json = json_encode($response, JSON_THROW_ON_ERROR, 512);
        
        header('Content-Type: application/json');
        
        return $json;
    }
    
    /**
     * @param callable $callable
     * @return $this
     */
    public function before(callable $callable): self
    {
        $this->beforeCallbacks[] = $callable;

        return $this;
    }
    
    /**
     * @param callable $callable
     * @return $this
     */
    public function after(callable $callable): self
    {
        $this->afterCallbacks[] = $callable;
    
        return $this;
    }
    
    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
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
     * @param Request $request
     * @param array $arguments
     */
    protected function callBeforeCallbacks(Request $request, array &$arguments): void
    {
        foreach ($this->beforeCallbacks as $before) {
            $before($request, $arguments);
        }
    }
    
    /**
     * @param Request $request
     * @param array $arguments
     * @param mixed $result
     * @return mixed
     */
    protected function callAfterCallbacks(Request $request, array $arguments, $result)
    {
        foreach ($this->afterCallbacks as $after) {
            $afterResult = $after($request, $arguments, $result);
            
            $result = $result ?? $afterResult;
        }
        
        return $result;
    }
}
