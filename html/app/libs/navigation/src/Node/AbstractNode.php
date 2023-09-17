<?php

namespace matpoppl\Navigation\Node;

use matpoppl\Navigation\Utils\ArrayObject;

abstract class AbstractNode
{
    /** @var array */
    private $attributes;
    /** @var array */
    private $options;
    
    public function __construct(array $config = null)
    {
        if (null === $config) {
            $this->attributes = new Attributes();
            $this->options = new ArrayObject();
        } else {
            $this->attributes = new Attributes($config['attributes'] ?? null);
            $this->options = new ArrayObject($config['options'] ?? null);
        }
    }
    
    /** @return Attributes */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /** @return ArrayObject */
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setAttributes(array $attributes)
    {
        $this->attributes = new ArrayObject($attributes);
        return $this;
    }
    
    public function setOptions(array $options)
    {
        $this->options = new ArrayObject($options);
        return $this;
    }
}
