<?php

namespace matpoppl\ImageCaptcha;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class ImageCaptchaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        if (0 === count($args)) {
            $config = $container->get('config');
            if (isset($config['image_captcha'])) {
                $args = [ $config['image_captcha'] ];
            }
        }
        
        return new $name(...$args);
    }
}
