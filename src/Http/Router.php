<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Http;

use Izzle\Translation\Services\Translation;
use JTL\Plugin\PluginInterface;
use Jtl\Shop5Router\Http\Error\ErrorTranslator;
use Jtl\Shop5Router\Traits\ErrorTranslatable;
use Jtl\Shop5Router\Traits\Translatable;
use Throwable;
use function json_decode;
use function json_encode;
use Jtl\Shop5Router\Traits\Shopable;
use Jtl\Shop5Router\Traits\Pluginable;
use Shop;
use JsonException;
use RuntimeException;
use Illuminate\Http\Request;

/**
 * Class Router
 * @package Jtl\Shop5Router\Http
 */
class Router
{
    use Shopable, Pluginable, ErrorTranslatable, Translatable;
    
    /**
     * @var string
     */
    protected string $controllerPath;
    
    /**
     * @var Route|null
     */
    protected ?Route $route = null;
    
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
     * @param Shop|null $shop
     * @param PluginInterface|null $plugin
     * @param ErrorTranslator|null $errorTranslator
     * @param Translation|null $translator
     */
    public function __construct(
        string $controllerPath,
        ?Shop $shop = null,
        ?PluginInterface $plugin = null,
        ?ErrorTranslator $errorTranslator = null,
        ?Translation $translator = null) {
        $this->setControllerPath($controllerPath);
    
        if ($shop !== null) {
            $this->setShop($shop);
        }
    
        if ($plugin !== null) {
            $this->setPlugin($plugin);
        }
        
        if ($errorTranslator !== null) {
            $this->setErrorTranslator($errorTranslator);
        }
        
        if ($translator !== null) {
            $this->setTranslator($translator);
        }
    }
    
    /**
     * @throws JsonException
     * @param bool $withHeader
     * @return string
     */
    public function send(bool $withHeader = true): string
    {
        $request = Request::createFromGlobals();
        $arguments = $request->getMethod() === Request::METHOD_POST ?
            json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR) : $request->query->all();
        
        $this->route = new Route(
            $this->getControllerPath(),
            $arguments['action'] ?? '',
            $this->getShop(),
            $this->getPlugin(),
            $this->getTranslator(),
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
    
        $json = json_encode($response, JSON_THROW_ON_ERROR);
        
        if ($withHeader) {
            header('Content-Type: application/json');
        }
        
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
     * @throws RuntimeException
     */
    public function getRoute(): Route
    {
        if ($this->route === null) {
            throw new RuntimeException('Route has not yet been instantiated');
        }
        
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
