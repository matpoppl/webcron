<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;
use matpoppl\Form\Element\CheckboxElement;
use Psr\Container\ContainerInterface;

/**
 * @property CheckboxElement $element
 */
class CheckboxView extends AbstractControlView
{
    protected $templateBlocks = '<span class="checkbox-wrap">{{VIEW}}{{LABEL}}</span>{{MSG}}';
    
    public function renderView()
    {
        $elem = $this->getElement();
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());

        $checked = $this->element->getValueChecked();
        
        $attrs->set('type', 'checkbox');
        $attrs->set('value', $checked);
        $attrs->set('checked', $checked == $elem->getValue());
        
        $hiddenAttrs = HTMLAttributes::create([
            'name' => $attrs->get('name'),
            'value' => $this->element->getValueUnchecked(),
            'type' => 'hidden',
        ]);
        
        return '<input' . $hiddenAttrs . ' /><input' . $attrs . ' />';
    }
}
