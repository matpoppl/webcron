<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\InputView;
use Psr\Container\ContainerInterface;

class CsrfElement extends InputElement
{
    public function __construct(ContainerInterface $container, array $options)
    {
        $options['attributes']['type'] = 'hidden';
        $this->setValue($container->get('csrf.manager')->getHash());
        parent::__construct($container, $options);
    }
    
    public function getViewType(): string
    {
        return InputView::class;
    }
}
