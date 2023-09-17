<?php

namespace matpoppl\Navigation\Utils;

class ArrayObject implements \Countable, \IteratorAggregate, \ArrayAccess
{

    /** @var array */
    private $data;

    public function __construct(array $data = null)
    {
        $this->data = (null === $data) ? [] : $data;
    }

    public function has(string $id)
    {
        return array_key_exists($id, $this->data);
    }

    public function get(string $id, $default = null)
    {
        return array_key_exists($id, $this->data) ? $this->data[$id] : $default;
    }

    public function set(string $id, $val)
    {
        $this->data[$id] = $val;
        return $this;
    }

    public function remove(string $id)
    {
        if (array_key_exists($id, $this->data)) {
            unset($this->data[$id]);
        }
        return $this;
    }
    
    public function getKeys()
    {
        return array_keys($this->data);
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
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }
}
