<?php
namespace matpoppl\Form\View;

use matpoppl\Form\Element\ElementInterface;
use Psr\Container\ContainerInterface;

class ElementViewFactory implements ElementViewFactoryInterface
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function create(ElementInterface $element) : ElementlViewInterface
    {
        $className = $element->getViewType();
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException("ViewType class not found `{$className}`");
        }
        
        if (! is_subclass_of($className, ElementlViewInterface::class)) {
            throw new \UnexpectedValueException("Unsupported ViewType `{$className}`");
        }
        
        return new $className($this->container, $element, $this);
    }
}
