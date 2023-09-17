<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Element\ElementInterface;

interface ElementlViewInterface extends \ArrayAccess
{
    /** @return ElementInterface */
    public function getElement();
    
    /** @return string */
    public function renderView();
    
    /** @return string */
    public function getViewType();
}
