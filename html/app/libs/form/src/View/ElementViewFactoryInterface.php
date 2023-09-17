<?php
namespace matpoppl\Form\View;

use matpoppl\Form\Element\ElementInterface;

interface ElementViewFactoryInterface
{
    public function create(ElementInterface $element) : ElementlViewInterface;
}
