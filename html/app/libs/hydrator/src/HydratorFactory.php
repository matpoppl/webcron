<?php

namespace matpoppl\Hydrator;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\Hydrator\NamingStrategy\NamingStrategyFactory;

class HydratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $options = count($args) > 0 ? $args[0] : [];
        
        return $this->create([
            'type' => $name,
            'options' => $options,
        ]);
    }
    
    public function create($options)
    {
        if (is_string($options)) {
            $className = $options;
            $options = [];
        } else if (! is_array($options)) {
            throw new \UnexpectedValueException('Unsupported Hydrator options type');
        } else {
            $className = $options['type'] ?? '';
        }
        
        $factory = new NamingStrategyFactory();
        
        $config = $options['options'] ?? [];
        
        if (! class_exists($className)) {
            $className = __NAMESPACE__ . '\\' . $className . 'Hydrator';
        }
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException('Unsupported hydrator type `'.$className.'`');
        }
        
        if (! is_subclass_of($className, HydratorInterface::class)) {
            throw new \UnexpectedValueException('Unsupported hydrator declaration');
        }
        
        $ret = new $className();
        
        if (isset($config['setterNamingStrategy'])) {
            $ret->setSetterNamingStrategy($factory->create($config['setterNamingStrategy']));
        }
        
        if (isset($config['getterNamingStrategy'])) {
            $ret->setGetterNamingStrategy($factory->create($config['getterNamingStrategy']));
        }
        
        return $ret;
    }
}
