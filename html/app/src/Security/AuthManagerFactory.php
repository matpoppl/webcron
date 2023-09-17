<?php

namespace App\Security;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class AuthManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new $name(new AuthRepository($container->get('entity.manager')), new PasswordHasher());
    }
}
