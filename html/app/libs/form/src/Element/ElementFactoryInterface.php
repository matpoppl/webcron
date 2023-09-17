<?php

namespace matpoppl\Form\Element;

interface ElementFactoryInterface
{
    public function createElement(array $options) : ElementInterface;
}
