<?php

namespace matpoppl\Form;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class FormBuilderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new FormBuilder($container, ...$args);
    }
}
