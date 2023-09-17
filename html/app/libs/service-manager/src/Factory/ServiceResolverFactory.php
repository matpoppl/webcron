<?php
namespace matpoppl\ServiceManager\Factory;

use Psr\Container\ContainerInterface;

class ServiceResolverFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        foreach (array_keys($args) as $i) {
            if (! is_string($args[$i])) {
                continue;
            }
            
            $arg = $args[$i];
            
            if ('@' === $arg[0]) {
                $args[$i] = $container->get(substr($arg, 1));
            } else if ('%' === $arg[0]) {
                $args[$i] = $this->getConfigValueFor($container, $arg);
            }
        }
        
        return new $name(...$args);
    }
    
    public function getConfigValueFor(ContainerInterface $container, $id)
    {
        $config = $container->get('config');
        
        $matched = null;
        if (preg_match_all('#%([^%]+)%#', $id, $matched) < 1) {
            throw new \UnexpectedValueException('Invalid config key `'.$id.'`');
        }
        
        foreach ($matched[1] as $i => $configId) {
            if (! array_key_exists($configId, $config)) {
                throw new \UnexpectedValueException('Config key dont exists `'.$configId.'`');
            }
            
            $val = $config[$configId];
            
            $id = is_string($val) ? str_replace($matched[0][$i], $val, $id) : $val;
        }
        
        return $id;
    }
}
