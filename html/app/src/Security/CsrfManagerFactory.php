<?php

namespace App\Security;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\HttpSession\SessionManager;

class CsrfManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $sm = $container->get('session.manager');
        
        if (! ($sm instanceof SessionManager)) {
            throw new \UnexpectedValueException('Unsupported session.manager type');
        }
        
        $sm->start();
        
        return new CsrfManager($sm->get($name), ...$args);
    }
}
