<?php

namespace matpoppl\EventManager;

class Event implements EventInterface
{
    private $name;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
}
