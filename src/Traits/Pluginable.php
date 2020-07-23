<?php declare(strict_types=1);

namespace JTL\Shop5Router\Traits;

use JTL\Plugin\PluginInterface;

/**
 * Trait Pluginable
 * @package JTL\Shop5Router\Traits
 */
trait Pluginable
{
    /**
     * @var PluginInterface|null
     */
    protected ?PluginInterface $plugin = null;
    
    /**
     * @return PluginInterface|null
     */
    public function plugin(): ?PluginInterface
    {
        return $this->plugin;
    }
    
    /**
     * @return PluginInterface|null
     */
    public function getPlugin(): ?PluginInterface
    {
        return $this->plugin;
    }
    
    /**
     * @param PluginInterface $plugin
     * @return self
     */
    public function setPlugin(PluginInterface $plugin): self
    {
        $this->plugin = $plugin;
        return $this;
    }
}
