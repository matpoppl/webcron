<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;

class InputView extends AbstractControlView
{
    public function renderFormRow()
    {
        switch ($this->element->getAttributes()->get('type')) {
            case 'hidden':
                $this->templateRow = '{{BLOCKS}}';
                $this->templateBlocks = '{{VIEW}}{{MSG}}';
                break;
            case 'checkbox':
            case 'radio':
                $this->templateBlocks = '<span class="checkbox-wrap">{{VIEW}}{{LABEL}}</span>{{MSG}}';
                break;
        }
        
        return parent::renderFormRow();
    }
    
    public function renderView()
    {
        $elem = $this->getElement();
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());
        if (! $attrs->has('type')) {
            $attrs->set('type', 'text');
        }

        $attrs->set('value', 'password' === $attrs->get('type') ? null : $elem->getValue());
        
        return '<input' . $attrs . ' />';
    }
}
