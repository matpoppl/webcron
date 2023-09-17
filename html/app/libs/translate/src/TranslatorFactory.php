<?php

namespace matpoppl\Translate;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class TranslatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $locale = $container->get('locale');
        $sources = $container->get('translation.source.manager');
        
        return new $name($locale, $sources);
    }

}
