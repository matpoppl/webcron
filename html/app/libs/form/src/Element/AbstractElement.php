<?php
namespace matpoppl\Form\Element;

use matpoppl\Form\Utils\ArrayObject;
use Psr\Container\ContainerInterface;

abstract class AbstractElement implements ElementInterface
{
    /** @var string */
    protected $type;
    
    /** @var ArrayObject */
    protected $options;
    
    /** @var ArrayObject */
    protected $attributes;
    
    /** @var string[][] */
    protected $messages = [];
    
    /** @var ContainerInterface */
    protected $container;
    
    public function __construct(ContainerInterface $container, array $options)
    {
        $this->container = $container;
        $this->type = $options['type'] ?? null;
        $this->options = new ArrayObject($options['options'] ?? null);
        $this->attributes = new ArrayObject($options['attributes'] ?? null);
        $this->messages = $options['messages'] ?? [];
    }
    
    public function getType() : string
    {
        return $this->type;
    }
    
    public function getOptions() : ArrayObject
    {
        return $this->options;
    }
    
    public function getAttributes() : ArrayObject
    {
        return $this->attributes;
    }
    
    /**
     * 
     * @param array|ArrayObject $attrs
     * @throws \InvalidArgumentException
     * @return static
     */
    public function setAttributes($attrs)
    {
        if ($attrs instanceof ArrayObject) {
            $this->attributes = $attrs;
        } else if (is_array($attrs)) {
            $this->attributes = new ArrayObject($attrs);
        } else {
            throw new \InvalidArgumentException('Unsupported attributes type');
        }
        
        return $this;
    }
    
    public function getMessageTypes(): array
    {
        return array_keys($this->messages);
    }
    
    public function hasMessagesOf(string $type) : bool
    {
        return ! empty($this->messages[$type]);
    }
    
    public function getMessagesOf(string $type) : array
    {
        return array_key_exists($type, $this->messages) ? $this->messages[$type] : [];
    }
    
    public function setMessagesOf(string $type, array $messages)
    {
        $this->messages[$type] = $messages;
        return $this;
    }
}
