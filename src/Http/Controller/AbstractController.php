<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Http\Controller;

use Izzle\Translation\Services\Translation;
use JTL\Plugin\Plugin;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use InvalidArgumentException;
use Jtl\Shop5Router\Traits\Pluginable;
use Jtl\Shop5Router\Traits\Shopable;
use Jtl\Shop5Router\Traits\Translatable;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 * @package Jtl\Shop5Router\Http\Controller
 */
abstract class AbstractController implements ControllerInterface
{
    use Shopable, Pluginable, Translatable;
    
    /**
     * @param Shop|null $shop
     * @param PluginInterface|null $plugin
     * @param Translation|null $translator
     */
    public function __construct(Shop $shop = null, PluginInterface $plugin = null, ?Translation $translator = null)
    {
        if ($shop !== null) {
            $this->setShop($shop);
        }
        
        if ($plugin !== null) {
            $this->setPlugin($plugin);
        }
        
        if ($translator !== null) {
            $this->setTranslator($translator);
        }
    }
    
    /**
     * @param Request $request
     * @param string $method
     * @param array $parameter
     * @return mixed
     */
    public function call(Request $request, string $method, array $parameter = [])
    {
        if (!is_callable([$this, $method])) {
            throw new InvalidArgumentException(sprintf('Method %s is not callable', $method));
        }
    
        array_unshift($parameter, $request);
        
        return call_user_func_array([$this, $method], array_values($parameter));
    }
    
    /**
     * @param int $pluginId
     * @return Plugin
     */
    protected function initPlugin(int $pluginId): Plugin
    {
        $plugin = new Plugin();
        $plugin->setID($pluginId);
        
        return $plugin;
    }
}
