<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http\Controller;

use Exception;
use Izzle\Translation\ParameterEnclosure;
use Izzle\Translation\Services\Translation;
use JTL\Plugin\Plugin;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use InvalidArgumentException;
use JTL\Shop5Router\Traits\Pluginable;
use JTL\Shop5Router\Traits\Shopable;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 * @package JTL\Shop5Router\Http\Controller
 */
abstract class AbstractController implements ControllerInterface
{
    use Shopable, Pluginable;
    
    /**
     * @param Shop $shop
     * @param PluginInterface $plugin
     */
    public function __construct(Shop $shop, PluginInterface $plugin)
    {
        $this->setShop($shop);
        $this->setPlugin($plugin);
    
        try {
            Translation::load(
                sprintf('%s/../../../resources/lang/%s.json', __DIR__, $this->shop->_Language()->getIso()),
                new ParameterEnclosure(),
                $this->shop->_Language()->getIso()
            );
        } catch (Exception $e) {
            $this->log($e->getMessage(), Logger::ERROR);
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
        
        return call_user_func_array([$this, $method], $parameter);
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
