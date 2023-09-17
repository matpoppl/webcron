<?php

namespace matpoppl\SmallMVC\Router;

use Psr\Http\Message\RequestInterface;

class Router
{
    private $options;
    /** @var Route\RouteInterface[] */
    private $routes = null;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /** @return Route\RouteInterface[] */
    public function getRoutes()
    {
        if (null === $this->routes) {
            $factory = new Route\RouteFactory();
            $this->routes = [];
            $routes = $this->options['routes'] ?? [];
            foreach ($routes as $name => $route) {
                $this->routes[$name] = $factory->create($route);
            }
        }

        return $this->routes;
    }
    
    public function hasMatch(RequestInterface $req)
    {
        foreach ($this->getRoutes() as $name => $route) {
            $match = $route->hasMatch($req);
            
            if (null !== $match) {
                $match->setParam('_route_name', $name);
                return $match;
            }
        }
        
        return null;
    }
    
    public function get($name)
    {
        if (! array_key_exists($name, $this->routes)) {
            throw new \DomainException('Route dont exists ' . $name);
        }
        
        return $this->routes[$name];
    }
}