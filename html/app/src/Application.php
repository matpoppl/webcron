<?php

namespace App;

use matpoppl\SmallMVC\Application as BaseApplication;
use matpoppl\ServiceManager\ServiceManager;
use matpoppl\SmallMVC\AppConfig;

class Application extends BaseApplication
{
    public static function create(array $config)
    {
        if (! array_key_exists('service_manager', $config)) {
            throw new \InvalidArgumentException('ServiceManger config required');
        }

        $sm = new ServiceManager($config['service_manager']);
        unset($config['service_manager']);

        $sm->set('config', new AppConfig($config));
        $app = new self($sm);
        return $app;
    }
}
