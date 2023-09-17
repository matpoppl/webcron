<?php

namespace matpoppl\Form\View;

use Psr\Container\ContainerInterface;
use matpoppl\Form\Element\ContainerInterface as ElementContainer;
use matpoppl\Form\Render\HTMLAttributes;

/**
 * @property ElementContainer $element
 */
class FormView extends AbstractContainerView
{
    public function __construct(ContainerInterface $container, ElementContainer $element, ElementViewFactoryInterface $viewElementFactory = null)
    {
        $this->container = $container;
        $this->element = $element;
        $this->viewElementFactory = $viewElementFactory;
    }
    
    public function renderContainerStart(array $attributes = null)
    {
        // @TODO first error or first editable
        // autofocus
        foreach ($this->element->getFieldList() as $name) {
            $this->element->get($name)->getAttributes()->set('autofocus', true);
            break;
        }
        
        $attrs = new HTMLAttributes($this->element->getAttributes()->getArrayCopy());
        
        if (!empty($attributes)) {
            $attrs->merge($attributes);
        }
        
        return '<form'.$attrs->render().'>';
    }
    
    public function renderContainerEnd()
    {
        return '</form>';
    }
    
    public function renderForm(array $attrs = null)
    {
        return $this->renderFormRow($attrs);
    }
}
