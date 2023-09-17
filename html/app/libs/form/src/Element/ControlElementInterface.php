<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\Utils\ArrayObject;

interface ControlElementInterface extends ElementInterface
{
    /** @return string */
    public function getType() : string;
    
    /** @return \matpoppl\Form\Utils\ArrayObject */
    public function getAttributes() : ArrayObject;
    
    /** @return \matpoppl\Form\Utils\ArrayObject */
    public function getOptions() : ArrayObject;
    
    public function getValue();
    
    public function setValue($value);
}
