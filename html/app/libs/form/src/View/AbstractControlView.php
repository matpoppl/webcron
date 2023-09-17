<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;
use function matpoppl\Form\Render\escape;

/**
 * @method \matpoppl\Form\Element\ControlElementInterface getElement()
 */
abstract class AbstractControlView extends AbstractElementView
{
    protected $templateBlocks = '{{LABEL}}{{VIEW}}{{MSG}}';
    
    public function renderLabel()
    {
        $elem = $this->getElement();
        
        $opts = $elem->getOptions();
        
        if (! $opts['label']) {
            return '';
        }
        
        $attrs = new HTMLAttributes([
            'for' => $elem->getAttributes()->get('id'),
        ]);

        if (isset($opts['label_attrs'])) {
            $attrs->merge($opts['label_attrs']);
        }

        return '<label' . $attrs->render() . '>' . escape($opts['label']) . '</label>';
    }
    
    public function renderFormRow()
    {
        $msgs = $this->renderMessagesOf('error') . $this->renderMessagesOf('desc');
        
        if (!empty($msgs)) {
            $msgs = strtr($this->templateMessages, [
                '{{MSG}}' => $msgs,
            ]);
        }
        
        $parts = [
            '{{LABEL}}' => $this->renderLabel(),
            '{{VIEW}}' => $this->renderView(),
            '{{MSG}}' => $msgs,
        ];
        
        $rowAttrs = HTMLAttributes::create();
        
        $rowAttrs->setConditionally('class', [
            'form__row',
            'form__row--' . $this->getViewType(),
            'form__row--required' => $this->getElement()->getAttributes()->get('required'),
            'form__row--disabled' => $this->getElement()->getAttributes()->get('disabled'),
            'form__row--error' => $this->getElement()->hasMessagesOf('error'),
        ]);
        
        return strtr($this->templateRow, [
            '{{ATTRS}}' => '' . $rowAttrs,
            '{{BLOCKS}}' => strtr($this->templateBlocks, $parts),
        ]);
    }
}
