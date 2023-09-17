<?php

namespace matpoppl\HttpSession;

use Psr\Container\ContainerInterface;

class NamespaceContainer implements ContainerInterface
{
    /** @var SessionNamespaceInterface[] */
    private $namespaces = [];
    
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->namespaces);
    }
    
    public function get(string $id)
    {
        if (! array_key_exists($id, $this->namespaces)) {
            if (! array_key_exists($id, $_SESSION)) {
                $_SESSION[$id] = [];
            }
            $this->namespaces[$id] = new SessionNamespace($id, $_SESSION[$id]);
        }
        
        return $this->namespaces[$id];
    }
    
    public function write()
    {
        foreach ($this->namespaces as $ns => $namespace) {
            $_SESSION[$ns] = $namespace->getArrayCopy();
        }
        return $this;
    }
    
    public function reset()
    {
        foreach (array_intersect_key($this->namespaces, $_SESSION) as $ns => $namespace) {
            $namespace->exchangeArray($_SESSION[$ns]);
        }
        return $this;
    }
    
    public function destroy()
    {
        foreach ($this->namespaces as $namespace) {
            $namespace->destroy();
        }
        
        $this->namespaces = [];
        
        return $this;
    }
}
