<?php

namespace matpoppl\HttpMessage\ParsedBody;

abstract class AbstractData implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array */
    private $data = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }
    
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function remove($key)
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
        
        return $this;
    }
    
    public function getArrayCopy()
    {
        return $this->data;
    }
    
    public function exchangeArray(array $data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function count(): int
    {
        return count($this->data);
    }
    
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->data);
    }
    
    public function offsetExists($offset) : bool
    {
        return $this->has($offset);
    }
    
    public function offsetGet($offset) : mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) : void
    {
        $this->set($offset, $value);
    }
    
    public function offsetUnset($offset) : void
    {
        $this->remove($offset);
    }
}
