<?php

namespace matpoppl\SmallMVC\View\Helper;

use matpoppl\SmallMVC\Router\Router;
use Psr\Container\ContainerInterface;

/**
 * @deprecated
 */
class PathHelper extends AbstractHelper
{
    /** @var Router */
    private $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    public function __invoke($name, array $params = null, array $query = null)
    {
        return $this->build($name, $params);
    }
    
    public function build($name, array $params = null, array $query = null)
    {
        $route = $this->router->get($name);
        return $route->buildPath($params);
    }
    
    /**
     * 
     * @param string $name
     * @return \matpoppl\SmallMVC\Router\Route\RouteInterface
     */
    public function getRoute($name)
    {
        return $this->router->get($name);
    }
    
    public static function create(ContainerInterface $container, ...$args)
    {
        return new static($container->get('router'));
    }
}
