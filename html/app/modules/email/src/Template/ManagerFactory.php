<?php

namespace matpoppl\Email\Template;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class ManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $config = $container->get('config');
        return new $name($config['mailer']);
    }
}
