<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\CheckboxView;

class CheckboxElement extends AbstractControlElement
{
    /** @var string */
    private $valueChecked = '1';
    /** @var string */
    private $valueUnchecked = '0';
    
    /**
     * @return string
     */
    public function getValueChecked()
    {
        return $this->valueChecked;
    }

    /**
     * @return string
     */
    public function getValueUnchecked()
    {
        return $this->valueUnchecked;
    }

    /**
     * @param string $valueChecked
     */
    public function setValueChecked($valueChecked)
    {
        $this->valueChecked = $valueChecked;
    }

    /**
     * @param string $valueUnchecked
     */
    public function setValueUnchecked($valueUnchecked)
    {
        $this->valueUnchecked = $valueUnchecked;
    }

    public function getViewType(): string
    {
        return CheckboxView::class;
    }
}
