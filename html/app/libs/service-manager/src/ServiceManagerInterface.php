<?php

namespace matpoppl\ServiceManager;

use Psr\Container\ContainerInterface;

interface ServiceManagerInterface extends ContainerInterface
{
    public function create(string $id, ...$args);
    public function addAliases(array $aliases);
    public function addFactories(array $factories);
    public function set(string $id, $value);
}
