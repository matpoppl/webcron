<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\SelectView;

class SelectElement extends AbstractMultiControlElement
{
    public function getViewType(): string
    {
        return SelectView::class;
    }
}
