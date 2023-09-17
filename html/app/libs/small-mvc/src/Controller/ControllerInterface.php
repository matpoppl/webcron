<?php

namespace matpoppl\SmallMVC\Controller;

use Psr\Container\ContainerInterface;
use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SmallMVC\Message\RequestInterface;

interface ControllerInterface
{
    public function setContainer(ContainerInterface $container);

    public function dispatch(MatchInterface $match, RequestInterface $request);
}
