<?php

namespace matpoppl\DBAL;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class BulkToolFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param mixed ...$args
     * @return BulkTool
     */
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        if (empty($args)) {
            $args = [$container->get('dbal')];
        }
        return new $name(...$args);
    }
}
