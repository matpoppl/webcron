<?php

namespace matpoppl\Hydrator\NamingStrategy;

class CamelCase2UnderscoreNamingStrategy implements NamingStrategyInterface
{
    public function __invoke($str)
    {
        $key = preg_replace('#([A-Z])#', '_$1', $str);
        $key = strtolower($key);
        return trim($key, '_');
    }
}
