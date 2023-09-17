<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\InputView;

class InputElement extends AbstractControlElement
{
    public function getViewType(): string
    {
        return InputView::class;
    }
}
