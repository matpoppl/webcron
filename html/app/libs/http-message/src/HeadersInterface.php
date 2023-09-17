<?php

namespace matpoppl\HttpMessage;

interface HeadersInterface
{
    /**
     * 
     * @param string $name
     * @return bool
     */
    public function has($name);
    
    /**
     *
     * @param string $name
     * @return string[]
     */
    public function get($name);
    
    /**
     *
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function set($name, $value);
    
    /**
     *
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function add($name, $value);
    
    /**
     *
     * @param string $name
     * @return string
     */
    public function getLine($name);
    
    
    /** @return string[][] */
    public function toArray();
}
