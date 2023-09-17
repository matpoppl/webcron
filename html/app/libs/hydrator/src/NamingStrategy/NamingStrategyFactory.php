<?php

namespace matpoppl\Hydrator\NamingStrategy;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class NamingStrategyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new $name();
    }
    
    public function create($options)
    {
        if (is_string($options)) {
            $className = $options;
            $options = [];
        } else if (! is_array($options)) {
            throw new \UnexpectedValueException('Unsupported NamingStrategy options type');
        } else {
            $className = $options['type'] ?? '';
        }
        
        if (! class_exists($className)) {
            $className = __NAMESPACE__ . '\\' . $className . 'NamingStrategy';
        }
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException('Unsupported NamingStrategy type `'.$className.'`');
        }
        
        if (! is_subclass_of($className, NamingStrategyInterface::class)) {
            throw new \UnexpectedValueException('Unsupported NamingStrategy declaration');
        }
        
        return new $className();
    }
}
