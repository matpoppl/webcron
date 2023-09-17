<?php

namespace matpoppl\ServiceManager\Factory;

use Psr\Container\ContainerInterface;

class InvokableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        if (! class_exists($name)) {
            throw new \UnexpectedValueException('Class dont exists `' . $name . '`');
        }

        return new $name(...$args);
    }
}
