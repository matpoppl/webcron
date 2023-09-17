<?php

namespace matpoppl\HttpSession;

interface SessionNamespaceInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * 
     * @param string $id
     * @return bool
     */
    public function has(string $id) : bool;
    
    /**
     * 
     * @param string $id
     * @param mixed|NULL $default
     * @return mixed
     */
    public function get(string $id, $default = null);
    
    /**
     * 
     * @param string $id
     * @param mixed $val
     * @return static
     */
    public function set(string $id, $val) : SessionNamespaceInterface;
    
    /**
     * 
     * @param string $id
     * @return static
     */
    public function remove(string $id) : SessionNamespaceInterface;
    
    /**
     *
     * @return string[]
     */
    public function getKeys() : array;
    
    /**
     *
     * @return array
     */
    public function getArrayCopy() : array;
    
    /**
     *
     * @param array $data
     */
    public function exchangeArray(array $data);

    public function destroy();
}
