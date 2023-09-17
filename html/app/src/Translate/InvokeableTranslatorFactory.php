<?php

namespace App\Translate;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class InvokeableTranslatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new InvokeableTranslator($container->get('translator'));
    }
}
