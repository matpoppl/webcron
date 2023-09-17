<?php

namespace App\Security;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class IdentityManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $sm = $container->get('session.manager');
        
        $options = count($args) > 0 ? $args[0] : $container->get('config')['identity_manager'];
        
        return new $name($sm->get($name), $options);
    }

}
