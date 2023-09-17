<?php

namespace matpoppl\SmallMVC\View\Helper;

use matpoppl\ServiceManager\ServiceManagerInterface;

class Container
{
    /** @var HelperInterface[]|object[] */
    private $helpers = [];
    /** @var ServiceManagerInterface */
    private $container;
    
    public function __construct(ServiceManagerInterface $container)
    {
        $this->container = $container;
    }
    
    public function get($name)
    {
        if (! array_key_exists($name, $this->helpers)) {
            $this->helpers[$name] = $this->container->create($name);
        }
        
        return $this->helpers[$name];
    }
}
