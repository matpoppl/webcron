<?php

namespace matpoppl\DBAL;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use Psr\Log\LoggerAwareInterface;

class DBALManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $config = $container->get('config');
        
        $options = empty($args) ? $config['dbal'] : $args[0];
        $logger = null;
        
        if (isset($options['logger']) && $container->has($options['logger'])) {
            $logger = $container->get($options['logger']);
            unset($options['logger']);
        }
        
        $ret = new $name($options);
        
        if (null !== $logger && $ret instanceof LoggerAwareInterface) {
            $ret->setLogger($logger);
        }
        
        return $ret;
    }
}
