<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Traits;

use Jtl\Shop5Router\Http\Error\ErrorTranslator;

/**
 * Trait ErrorTranslatable
 * @package Jtl\Shop5Router\Traits
 */
trait ErrorTranslatable
{
    /**
     * @var ErrorTranslator|null
     */
    protected ?ErrorTranslator $errorTranslator = null;
    
    /**
     * @return ErrorTranslator|null
     */
    public function errorTranslator(): ?ErrorTranslator
    {
        return $this->errorTranslator;
    }
    
    /**
     * @return ErrorTranslator|null
     */
    public function getErrorTranslator(): ?ErrorTranslator
    {
        return $this->errorTranslator;
    }
    
    /**
     * @param ErrorTranslator $errorTranslator
     * @return self
     */
    public function setErrorTranslator(ErrorTranslator $errorTranslator): self
    {
        $this->errorTranslator = $errorTranslator;
        return $this;
    }
}
