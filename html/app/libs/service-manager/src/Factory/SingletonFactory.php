<?php

namespace matpoppl\ServiceManager\Factory;

use Psr\Container\ContainerInterface;

class SingletonFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        if (! class_exists($name)) {
            throw new \UnexpectedValueException('Class dont exists `' . $name . '`');
        }
        
        if (! method_exists($name, 'getInstance')) {
            throw new \UnexpectedValueException('Class method `' . $name . '::getInstance()` dont exists ');
        }
        
        return $name::getInstance();
    }
}
