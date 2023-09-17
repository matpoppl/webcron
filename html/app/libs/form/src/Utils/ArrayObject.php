<?php
namespace matpoppl\Form\Utils;

class ArrayObject implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /** @var array */
    private $arrayData;

    public function __construct(array $arrayData = null)
    {
        $this->arrayData = (null === $arrayData) ? array() : $arrayData;
    }

    public function has(string $id)
    {
        return array_key_exists($id, $this->arrayData);
    }

    public function get(string $id, $default = null)
    {
        return array_key_exists($id, $this->arrayData) ? $this->arrayData[$id] : $default;
    }

    public function set(string $id, $value)
    {
        $this->arrayData[$id] = $value;
        return $this;
    }
    
    public function setConditionally(string $id, array $values)
    {
        $str = '';
        
        foreach ($values as $key => $value) {
            if (is_int($key)) {
                $str .= ' ' . $value;
            } else if ($value) {
                $str .= ' ' . $key;
            }
        }
        
        return $this->set($id, $str);
    }
    
    public function remove(string $id)
    {
        if (array_key_exists($id, $this->arrayData)) {
            unset($this->arrayData[$id]);
        }
        return $this;
    }
    
    /**
     * 
     * @param static|array $data
     * @throws \InvalidArgumentException
     * @return static
     */
    public function merge($data)
    {
        if ($data instanceof self) {
            $this->arrayData = $data->getArrayCopy();
        } else if (is_array($data)) {
            $this->arrayData = array_replace($this->arrayData, $data);
        } else {
            throw new \InvalidArgumentException('Unsupprted data type');
        }
        
        return $this;
    }

    public function getArrayCopy()
    {
        return $this->arrayData;
    }

    public function exchangeArray(array $data)
    {
        $this->arrayData = $data;
        return $this;
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
    
    /** @return string[] */
    public function getKeys()
    {
        return array_keys($this->arrayData);
    }
    
    public function count(): int
    {
        return count($this->arrayData);
    }

    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->arrayData);
    }
}
    