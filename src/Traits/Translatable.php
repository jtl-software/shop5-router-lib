<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Traits;

use Izzle\Translation\Services\Translation;

/**
 * Trait Translatable
 * @package Jtl\Shop5Router\Traits
 */
trait Translatable
{
    /**
     * @var Translation|null
     */
    protected ?Translation $translation = null;
    
    /**
     * @return Translation|null
     */
    public function translator(): ?Translation
    {
        return $this->getTranslator();
    }
    
    /**
     * @return Translation|null
     */
    public function getTranslator(): ?Translation
    {
        return $this->translation;
    }
    
    /**
     * @param Translation $translation
     * @return self
     */
    public function setTranslator(Translation $translation): self
    {
        $this->translation = $translation;
        
        return $this;
    }
}
