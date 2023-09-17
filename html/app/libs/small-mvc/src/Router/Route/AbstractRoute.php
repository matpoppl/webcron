<?php

namespace matpoppl\SmallMVC\Router\Route;

abstract class AbstractRoute implements RouteInterface
{
    protected $defaults = array();

    public function __construct(array $options)
    {
        $this->defaults = $options['defaults'] ?? [];
    }

    /** @return array */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     * @return static
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }
}