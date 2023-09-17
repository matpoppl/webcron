<?php

namespace matpoppl\Hydrator\NamingStrategy;

class Underscore2CamelCaseNamingStrategy implements NamingStrategyInterface
{
    public function __invoke($str)
    {
        $prop = str_replace(['_', '-'], ' ', $str);
        $prop = ucwords($prop);
        $prop = str_replace(' ', '', $prop);
        return lcfirst($prop);
    }
}
