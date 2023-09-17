<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;
use function matpoppl\Form\Render\escape;

class SelectView extends AbstractControlView
{
    public function renderView()
    {
        $elem = $this->getElement();
        
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());
        if (! $attrs->has('type')) {
            $attrs->set('type', 'button');
        }
        
        $options = '';
        
        $value = $elem->getValue();
        if (is_array($value)) {
            $selected = array_combine($value, $value);
        } else {
            $selected = [$value => $value];
        }
        
        $disabled = $elem->getDisabledOptions();
        $disabled = array_combine($disabled, $disabled);
        
        foreach ($elem->getMultiOptions() as $value => $option) {
            
            if (is_array($option)) {
                $label = $option['label'] ?? $value;
                $option['label'] = null;
                $option['value'] = $value;
                $attrsOpt = HTMLAttributes::create($option);
            } else {
                $label = $option;
                $attrsOpt = HTMLAttributes::create([
                    'value' => $value,
                ]);
            }
            
            $attrsOpt['selected'] = array_key_exists($value, $selected);
            $attrsOpt['disabled'] = array_key_exists($value, $disabled);
            
            $options .= '<option ' . $attrsOpt . '>' . escape($label) . '</option>';
        }
        
        if ($attrs['multiple']) {
            $attrs['name'] = $attrs['name'] . '[]';
        }
        
        return '<select ' . $attrs . '>' . $options . '</select>';
    }
}
