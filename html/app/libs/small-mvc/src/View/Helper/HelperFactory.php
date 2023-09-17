<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class HelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $className = $name;
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException('Helper class dont exists');
        }
        
        return $className::create($container, ...$args);
    }    
}
