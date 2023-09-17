<?php

namespace matpoppl\Hydrator\NamingStrategy;

interface NamingStrategyInterface
{
    public function __invoke($str);
}
