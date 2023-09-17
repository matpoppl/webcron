<?php

namespace matpoppl\Form\Element;

interface ContainerInterface extends ElementInterface
{
    
    /**
     * 
     * @param string $name
     * @return bool
     */
    public function has(string $name);
    
    /**
     * 
     * @param string $name
     * @return ElementInterface
     */
    public function get(string $name);
    
    /**
     * 
     * @param string $name
     * @param array|ElementInterface $elem
     * @return self
     */
    public function set(string $name, $elem);
    
    /**
     * 
     * @param string $name
     * @return self
     */
    public function remove(string $name);
    
    /**
     * 
     * @param string $name
     * @return bool
     */
    public function __isset($name);
    
    /**
     * 
     * @param string $name
     * @return ElementInterface
     */
    public function __get($name);
    
    /** @return string[] */
    public function getFieldList();

}
