<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;

class FieldsetView extends AbstractContainerView
{
    public function renderContainerStart()
    {
        $legendTag = '';
        
        if ($legend = $this->element->getOptions()->get('legend')) {
            $legendTag = '<legend>' . $legend . '</legend>';
        }
        
        $attrs = HTMLAttributes::create($this->element->getAttributes()->getArrayCopy());
        return '<fieldset'.$attrs->render().'>' . $legendTag;
    }
    
    public function renderContainerEnd()
    {
        return '</fieldset>';
    }
}
