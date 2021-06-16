<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Traits;

use Exception;
use JTL\Shop;
use Monolog\Logger;

/**
 * Trait Shopable
 * @package Jtl\Shop5Router\Traits
 */
trait Shopable
{
    /**
     * @var Shop|null
     */
    protected ?Shop $shop = null;
    
    /**
     * @return Shop|null
     */
    public function shop(): ?Shop
    {
        return $this->shop;
    }
    
    /**
     * @return Shop|null
     */
    public function getShop(): ?Shop
    {
        return $this->shop;
    }
    
    /**
     * @param Shop $shop
     * @return self
     */
    public function setShop(Shop $shop): self
    {
        $this->shop = $shop;
        return $this;
    }
    
    /**
     * @param string $message
     * @param int $level
     * @param array $context
     * @return self
     */
    public function log(string $message, int $level = Logger::DEBUG, array $context = []): self
    {
        if ($this->shop === null) {
            return $this;
        }

        try {
            $this->shop->_Container()->getLogService()->log($level, $message, $context);
        } catch (Exception $e) {
        }
        
        return $this;
    }
}
