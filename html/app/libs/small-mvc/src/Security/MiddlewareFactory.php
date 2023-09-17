<?php

namespace matpoppl\SmallMVC\Security;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class MiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $accessCheck = new AccessCheck($container->get('mvc.acl'));
        $identityManager = $container->get('identity.manager');
        $router = $container->get('router');
        
        return new $name($accessCheck, $identityManager, $router);
    }
}
