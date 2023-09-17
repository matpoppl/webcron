<?php

namespace matpoppl\DBAL;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class DbalFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $config = $container->get('config');
        return new $name( empty($args) ? $config['dbal']['options'] : $args[0] );
    }
}
