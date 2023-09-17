<?php

namespace matpoppl\HttpSession;

class SessionNamespace implements SessionNamespaceInterface
{
    /** @var string */
    private $ns;
    /** @var array */
    private $data;
    
    public function __construct(string $ns, array $data)
    {
        $this->ns = $ns;
        $this->data = (null === $data) ? [] : $data;
    }
    
    public function has(string $id) : bool
    {
        return array_key_exists($id, $this->data);
    }
    
    public function get(string $id, $default = null)
    {
        return array_key_exists($id, $this->data) ? $this->data[$id] : $default;
    }
    
    public function set(string $id, $val) : SessionNamespaceInterface
    {
        $this->data[$id] = $val;
        return $this;
    }
    
    public function remove(string $id) : SessionNamespaceInterface
    {
        if ($this->has($id)) {
            unset($this->data[$id]);
        }
        
        return $this;
    }
    
    public function offsetExists($id) : bool
    {
        return $this->has($id);
    }
    
    public function offsetGet($id) : mixed
    {
        return $this->get($id);
    }

    public function offsetSet($id, $val) : void
    {
        $this->set($id, $val);
    }
    
    public function offsetUnset($id) : void
    {
        $this->remove($id);
    }
    
    public function count(): int
    {
        return count($this->data);
    }
    
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->data);
    }
    
    public function getKeys() : array
    {
        return array_keys($this->data);
    }
    
    public function getArrayCopy() : array
    {
        return $this->data;
    }
    
    public function exchangeArray(array $data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function destroy()
    {
        $this->data = [];
        return $this;
    }
}
