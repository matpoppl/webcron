<?php

namespace matpoppl\Form\Element;

abstract class AbstractMultiControlElement extends AbstractControlElement implements MultiControlElementInterface
{
    public function isMultiple() : bool
    {
        return !! $this->getAttributes()->get('multiple');
    }
    
    public function getMultiOptions() : array
    {
        $ret = $this->getOptions()->has('multiOptions') ? $this->getOptions()->get('multiOptions') : [];
        return is_array($ret) ? $ret : [$ret];
    }
    
    public function getDisabledOptions() : array
    {
        return $this->getOptions()->get('disabledOptions') ?: [];
    }
}
