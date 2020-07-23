<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http\Error;

use Izzle\Model\Model;
use Izzle\Model\PropertyCollection;
use Izzle\Model\PropertyInfo;

/**
 * Class ErrorTranslatorItem
 * @package JTL\Shop5Router\Http\Error
 */
class ErrorTranslatorItem extends Model
{
    /**
     * @var int
     */
    protected int $code = 0;
    
    /**
     * @var string
     */
    protected string $message = '';
    
    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
    
    /**
     * @param int $code
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
    /**
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getMessage();
    }
    
    /**
     * @inheritDoc
     */
    protected function loadProperties(): PropertyCollection
    {
        return new PropertyCollection([
            new PropertyInfo('code', 'int'),
            new PropertyInfo('message')
        ]);
    }
}
