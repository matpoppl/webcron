<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\FormElements;
use Psr\Container\ContainerInterface as PsrContainer;

abstract class AbstractContainerElement extends AbstractElement implements ContainerInterface
{
    /** @var FormElements */
    protected $elements = [];
    
    public function __construct(PsrContainer $container, array $options)
    {
        $this->elements = new FormElements($container, $options['elements'] ?? []);
        parent::__construct($container, $options);
    }
    
    public function has($name)
    {
        return $this->elements->has($name);
    }
    
    public function get($name)
    {
        return $this->elements->get($name);
    }
    
    public function set($name, $elem)
    {
        return $this->elements->set($name, $elem);
    }
    
    public function remove($name)
    {
        return $this->elements->remove($name);
    }
    
    public function __isset($name)
    {
        return $this->elements->has($name);
    }
    
    public function __get($name)
    {
        return $this->elements->get($name);
    }
    
    /** @return string[] */
    public function getFieldList()
    {
        return $this->elements->getFieldList();
    }
    
    public function setValue($values)
    {
        if (! is_array($values)) {
            $values = [$values];
        }
        
        foreach ($this->getFieldList() as $name) {
            $elem = $this->get($name);
            
            if ($elem instanceof ContainerInterface) {
                $this->get($name)->setValue(array_key_exists($name, $values) ? $values[$name] : []);
            } else if ($elem instanceof ControlElementInterface) {
                $this->get($name)->setValue(array_key_exists($name, $values) ? $values[$name] : null);
            }
        }
        
        return $this;
    }
    
    public function setErrors(array $messages)
    {
        return $this->setMessagesOf('error', $messages);
    }
    
    public function setMessagesOf($type, array $messages)
    {
        $myMessages = [];
        
        foreach (array_keys($messages) as $name) {
            if (is_int($name)) {
                $myMessages[] = $messages[$name];
                continue;
            }
            
            if (! $this->has($name)) {
                continue;
            }
            
            $this->get($name)->setMessagesOf($type, $messages[$name]);
        }
        
        return parent::setMessagesOf($type, $myMessages);;
    }
}
