<?php

namespace matpoppl\SmallMVC\Router\Route;

use Psr\Http\Message\RequestInterface;
use matpoppl\SmallMVC\Router\MatchInterface;

interface RouteInterface
{
    public function hasMatch(RequestInterface $request) : ?MatchInterface;
    public function buildPath(array $params = null) : string;
}
