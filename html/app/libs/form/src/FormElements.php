<?php
namespace matpoppl\Form;

use matpoppl\Form\Element\ElementFactoryInterface;
use matpoppl\Form\Utils\ArrayObject;
use Psr\Container\ContainerInterface;

class FormElements implements \IteratorAggregate
{
    /** @var ArrayObject */
    private $elements;
    
    /** @var Element\ElementFactoryInterface */
    private $factory = null;
    
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container, array $elements)
    {
        $this->container = $container;
        $this->elements = new ArrayObject();
        $this->setElements($elements);
    }

    public function has($name)
    {
        return $this->elements->has($name);
    }

    public function get($name)
    {
        if (! $this->elements->has($name)) {
            throw new \DomainException("Element `{$name}` not found");
        }
        
        $elem = $this->elements->get($name);
        
        if (is_array($elem)) {
            $elem = $this->getFactory()->createElement($elem);
            $this->set($name, $elem);
        }
        
        if (! ($elem instanceof Element\ElementInterface)) {
            throw new \DomainException("Element `{$name}` not found");
        }
        
        return $elem;
    }

    public function set($name, $element)
    {
        $this->elements->set($name, $element);
        return $this;
    }

    public function remove($name)
    {
        if (! $this->elements->has($name)) {
            throw new \DomainException("Element `{$name}` not found");
        }
        $this->elements->remove($name);
        return $this;
    }
    
    public function setElements(array $elements)
    {
        $this->elements = new ArrayObject();
        foreach ($elements as $name => $element) {
            $this->set($name, $element);
        }
        return $this;
    }
    
    /** @return string[] */
    public function getFieldList()
    {
        return $this->elements->getKeys();
    }
    
    /** @return Element\ElementFactoryInterface */
    public function getFactory()
    {
        if (null === $this->factory) {
            $this->setFactory(new Element\ElementFactory($this->container));
        }
        
        return $this->factory;
    }
    
    public function setFactory(ElementFactoryInterface $factory)
    {
        $this->factory = $factory;
        return $this;
    }
    
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->elements);
    }
}
