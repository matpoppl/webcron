<?php

namespace matpoppl\Mailer;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\Mailer\Transport\TransportFactory;

class MailerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        if (count($args) > 0) {
            $options = $args[0];
        } else {
            $config = $container->get('config');
            $options = $config['mailer'] ?? [];
        }
        
        $transportFactory = new TransportFactory();
        
        return new $name( $transportFactory->create($options['transport'] ?? []) );
    }
}
