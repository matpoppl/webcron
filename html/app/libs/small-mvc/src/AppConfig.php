<?php

namespace matpoppl\SmallMVC;

use Psr\Container\ContainerInterface;

class AppConfig implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /** @var ContainerInterface */
    private $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function has($key)
    {
        return array_key_exists($key, $this->config);
    }
    
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }
    
    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }
    
    public function remove($key)
    {
        if (array_key_exists($key, $this->config)) {
            unset($this->config[$key]);
        }
    }
    
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }
    
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset);
    }
    
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }
    
    public function count(): int
    {
        return count($this->config);
    }
    
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->config);
    }
    
    public function merge(array $config)
    {
        $this->config = array_replace_recursive($this->config, $config);
    }
}
