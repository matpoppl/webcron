<?php
namespace matpoppl\Form\Element;

use matpoppl\Form\View\ButtonView;

class ButtonElement extends AbstractControlElement
{
    public function getViewType(): string
    {
        return ButtonView::class;
    }
}
