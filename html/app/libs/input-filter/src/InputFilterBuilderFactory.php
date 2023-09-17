<?php

namespace matpoppl\InputFilter;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class InputFilterBuilderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new InputFilterBuilder($container);
    }
}
