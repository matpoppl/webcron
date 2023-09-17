<?php

namespace matpoppl\ImageCaptcha;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;

class ImageCaptchaValidatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new $name($container->get(PhraseSourceInterface::class), ...$args);
    }
}
