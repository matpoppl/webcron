<?php

namespace matpoppl\Form\Element;

interface MultiControlElementInterface extends ControlElementInterface
{
    public function isMultiple() : bool;
    
    public function getMultiOptions() : array;
}
