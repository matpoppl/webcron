<?php

namespace matpoppl\EntityManager;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class EntityManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new EntityManager( $container, $container->get('config')['entity_manager'] );
    }    
}
