<?php

namespace matpoppl\HttpSession;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class SessionManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $cfg = $container->get('config');
        
        $defaults = $cfg['session_manager'] ?? [];
        
        $options = count($args) > 0 ? $args[0] : ($defaults['options'] ?? null);
        $config = count($args) > 1 ? $args[1] : ($defaults['config'] ?? null);
        
        return new SessionManager($options, $config);
    }
}
