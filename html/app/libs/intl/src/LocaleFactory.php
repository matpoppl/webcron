<?php

namespace matpoppl\Intl;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class LocaleFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $config = $container->get('config')['locale'] ?? [];
        return new $name( empty($args) ? $config : $args[0] );
    }
}
