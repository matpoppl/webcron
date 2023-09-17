<?php

namespace matpoppl\Translate\Source;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class SourceManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $config = $container->get('config')['translator'] ?? [];
        return new $name( empty($args) ? $config : $args[0], new SourceFactory($container) );
    }
}
