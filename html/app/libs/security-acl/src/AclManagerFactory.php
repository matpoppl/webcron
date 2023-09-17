<?php

namespace matpoppl\SecurityAcl;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class AclManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        /** @var AclInterface $acl */
        $acl = new $name();
        
        if (! $acl instanceof AclInterface) {
            return $acl;
        }
        
        $config = count($args) > 0 ? $args : $container->get('config')['security_acl'];
        
        if (! empty($config['roles'])) {
            foreach ($config['roles'] as $key => $val) {
                if (is_int($key)) {
                    $acl->addRole($val);
                } else {
                    $acl->addRole($key, $val);
                }
            }
        }
        
        if (! empty($config['resources'])) {
            foreach ($config['resources'] as $key => $val) {
                if (is_int($key)) {
                    $acl->addResource($val);
                } else {
                    $acl->addResource($key, $val);
                }
            }
        }
        
        if (! empty($config['allow'])) {
            foreach ($config['allow'] as $rule) {
                $acl->allow(...$rule);
            }
        }
        
        if (! empty($config['deny'])) {
            foreach ($config['deny'] as $rule) {
                $acl->deny(...$rule);
            }
        }
        
        return $acl;
    }
}
