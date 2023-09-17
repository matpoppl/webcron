<?php
namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;
use function matpoppl\Form\Render\escape;

class ButtonView extends AbstractElementView
{
    protected $templateRow = '<div class="form__btns">{{BLOCKS}}</div>';
    
    public function renderView()
    {
        $elem = $this->getElement();
        
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());
        if (! $attrs->has('type')) {
            $attrs->set('type', 'button');
        }
        
        if (!isset($attrs['value'])) {
            unset($attrs['id']);
            unset($attrs['name']);
        }
        
        return '<button ' . $attrs . '>' . escape($elem->getOptions()->get('label')) . '</button>';
    }
}
