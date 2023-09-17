<?php

namespace matpoppl\PathManager;

class Location implements LocationInterface
{
    /** @var string */
    private $pathname;
    
    public function __construct(string $pathname)
    {
        $this->pathname = rtrim($pathname, '\\/');
    }
    
    public function append(string $location) : LocationInterface
    {
        return new static($this->pathname . '/' . ltrim($location, '\\/'));
    }
    
    /** @return string */
    public function getPathname()
    {
        return $this->pathname;
    }
}
