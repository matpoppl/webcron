<?php

namespace matpoppl\ServiceManager\Factory;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args);
}
