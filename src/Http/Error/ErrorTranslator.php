<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http\Error;

use InvalidArgumentException;

/**
 * Class ErrorTranslator
 * @package JTL\Shop5Router\Http\Error
 */
class ErrorTranslator
{
    /**
     * @var array
     */
    protected array $collection = [];
    
    /**
     * @param ErrorTranslatorItem|ErrorTranslatorItem[] $trans
     * @return self
     * @throws InvalidArgumentException
     */
    public function add($trans): self
    {
        if (is_array($trans)) {
            foreach ($trans as $t) {
                $this->addItem($t);
            }
        } else {
            $this->addItem($trans);
        }
        
        return $this;
    }
    
    /**
     * @param int $code
     * @return bool
     */
    public function has(int $code): bool
    {
        return !empty($this->collection[$code]);
    }
    
    /**
     * @param int $code
     * @return ErrorTranslatorItem|null
     */
    public function get(int $code): ?ErrorTranslatorItem
    {
        if ($this->has($code)) {
            return $this->collection[$code];
        }
    
        return null;
    }
    
    /**
     * @param int $code
     * @return self
     */
    public function remove(int $code): self
    {
        if ($this->has($code)) {
            unset($this->collection[$code]);
        }
        
        return $this;
    }
    
    /**
     * @param $trans
     */
    protected function addItem($trans): void
    {
        if (!($trans instanceof ErrorTranslatorItem)) {
            throw new InvalidArgumentException(sprintf(
                'Array items must be instance of %s',
                ErrorTranslatorItem::class
            ));
        }
    
        $this->collection[$trans->getCode()] = $trans;
    }
}
