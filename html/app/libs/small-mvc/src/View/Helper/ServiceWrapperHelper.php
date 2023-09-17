<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;

class ServiceWrapperHelper extends AbstractHelper
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function __invoke($name, array $params = null)
    {
        return $this->get($name, $params);
    }
    
    public function get($name, array $params = null)
    {
        return $this->container->get($name);
    }
}
