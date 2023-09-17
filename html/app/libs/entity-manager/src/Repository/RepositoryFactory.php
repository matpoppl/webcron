<?php

namespace matpoppl\EntityManager\Repository;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class RepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new $name( $args[0], $container->get('dbal'), $container->get('query.builder') );
    }
}
