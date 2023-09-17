<?php

namespace matpoppl\SmallMVC\Router;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class RouterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new Router( empty($args) ? $container->get('config')['router'] : $args[0] );
    }
}
