<?php

namespace matpoppl\SmallMVC\View;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\PathManager\FallbackPathManager;
use matpoppl\PathManager\Location;

class ViewDataFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $defaultLocation = new Location($args[0]);
        
        $fallback = new FallbackPathManager($container->get('path.manager'), $defaultLocation);
        
        return new $name(new Helper\Container($container), $fallback);
    }
}
