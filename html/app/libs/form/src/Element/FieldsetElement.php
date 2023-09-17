<?php
namespace matpoppl\Form\Element;

use matpoppl\Form\View\FieldsetView;

class FieldsetElement extends AbstractContainerElement
{
    public function getViewType(): string
    {
        return FieldsetView::class;
    }
}
