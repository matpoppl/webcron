<?php

namespace matpoppl\Hydrator;

use matpoppl\Hydrator\NamingStrategy\NamingStrategyInterface;

interface HydratorInterface
{
    /**
     * 
     * @param array $data
     * @param object $obj
     */
    public function hydrate(array $data, $obj);
    
    /**
     * 
     * @param object $obj
     * @return array
     */
    public function extract($obj);
    
    /** @return NamingStrategyInterface|NULL */
    public function getGetterNamingStrategy();
    
    /** @return NamingStrategyInterface|NULL */
    public function getSetterNamingStrategy();
    
    /**
     * 
     * @param NamingStrategyInterface $ns
     * @return static
     */
    public function setGetterNamingStrategy(NamingStrategyInterface $ns);
    
    /**
     *
     * @param NamingStrategyInterface $ns
     * @return static
     */
    public function setSetterNamingStrategy(NamingStrategyInterface $ns);
}