<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;

abstract class AbstractHelper implements HelperInterface
{
    public static function create(ContainerInterface $container, ...$args)
    {
        return new static(...$args);
    }
}
