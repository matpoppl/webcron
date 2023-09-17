<?php

namespace matpoppl\SmallMVC\Router\Route;

use Psr\Http\Message\RequestInterface;
use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SmallMVC\Router\MatchResult;

class StaticRoute extends AbstractRoute
{
    private $path;

    public function __construct(array $options)
    {
        $this->path = $options['path'] ?? null;
        parent::__construct($options);
    }

    public function hasMatch(RequestInterface $request) : ?MatchInterface
    {
        $path = $request->getUri()->getPath();

        if ($path !== $this->path) {
            return null;
        }

        /*
        if (0 !== strpos($path, $this->path)) {
            return null;
        }
        */

        return new MatchResult($this->getDefaults());
    }
    
    public function buildPath(array $params = null): string
    {
        return $this->path;
    }
}
