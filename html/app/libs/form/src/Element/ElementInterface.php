<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\Utils\ArrayObject;

interface ElementInterface
{
    /** @return string */
    public function getType() : string;
    
    /** @return string */
    public function getViewType() : string;
    
    /** @return \matpoppl\Form\Utils\ArrayObject */
    public function getAttributes() : ArrayObject;
    
    /** @return \matpoppl\Form\Utils\ArrayObject */
    public function getOptions() : ArrayObject;
    
    public function getMessageTypes() : array;

    /**
     *
     * @param string $type
     * @return bool
     */
    public function hasMessagesOf(string $type) : bool;
    
    /**
     *
     * @param string $type
     * @return array
     */
    public function getMessagesOf(string $type) : array;
}
