<?php

namespace matpoppl\PathManager;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class PathManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $opts = count($args) > 0 ? $args[0] : $container->get('config')['path_manager'];
        return new $name($opts);
    }
}
