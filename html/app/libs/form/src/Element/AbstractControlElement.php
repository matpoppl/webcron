<?php

namespace matpoppl\Form\Element;

abstract class AbstractControlElement extends AbstractElement implements ControlElementInterface
{
    protected $value;
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
