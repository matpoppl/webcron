<?php

namespace matpoppl\Hydrator;

use matpoppl\Hydrator\NamingStrategy\NamingStrategyInterface;

abstract class AbstractHydrator implements HydratorInterface
{
    /** @var NamingStrategyInterface|NULL */
    protected $getterNamingStrategy = null;
    /** @var NamingStrategyInterface|NULL */
    protected $setterNamingStrategy = null;
    
    public function getGetterNamingStrategy()
    {
        return $this->getterNamingStrategy;
    }
    
    public function getSetterNamingStrategy()
    {
        return $this->setterNamingStrategy;
    }
    
    public function setGetterNamingStrategy(NamingStrategyInterface $ns)
    {
        $this->getterNamingStrategy = $ns;
        return $this;
    }
    
    public function setSetterNamingStrategy(NamingStrategyInterface $ns)
    {
        $this->setterNamingStrategy = $ns;
        return $this;
    }
}
