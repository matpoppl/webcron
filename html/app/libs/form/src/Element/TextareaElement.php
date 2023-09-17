<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\TextareaView;

class TextareaElement extends AbstractControlElement
{
    public function getViewType(): string
    {
        return TextareaView::class;
    }
}
