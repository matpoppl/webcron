<?php

namespace matpoppl\SmallMVC\Router\Route;

class RouteFactory
{
    public function create(array $options)
    {
        if (array_key_exists('constraints', $options)) {
            return new SegmentRoute($options);
        }
        
        if (array_key_exists('path', $options)) {
            return new StaticRoute($options);
        }
        
        throw new \InvalidArgumentException('Unsupported route type');
    }
}