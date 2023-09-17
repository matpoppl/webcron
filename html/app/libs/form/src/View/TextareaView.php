<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;
use function matpoppl\Form\Render\escape;

class TextareaView extends AbstractControlView
{
    public function renderView()
    {
        $elem = $this->getElement();
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());
        if (! $attrs->has('type')) {
            $attrs->set('type', 'text');
        }

        $value = escape($elem->getValue());
        return "<textarea{$attrs}>{$value}</textarea>";
    }
}
