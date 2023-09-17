<?php

namespace matpoppl\InputFilter;

use matpoppl\ServiceManager\ServiceManagerInterface;
use matpoppl\Translate\TranslatorInterface;

class InputFilter implements InputInterface
{
    /** @var InputContainer */
    private $inputs;
    
    public function __construct(ServiceManagerInterface $sm, array $options)
    {
        $this->inputs = new InputContainer($sm, $options['inputs'] ?? []);
    }
    
    public function has(string $name)
    {
        return $this->inputs->has($name);
    }
    
    public function get(string $name) : InputInterface
    {
        return $this->inputs->get($name);
    }
    
    /**
     *
     * @param string $name
     * @param array|InputInterface $input
     * @return \matpoppl\InputFilter\InputContainer
     */
    public function set(string $name, $input)
    {
        $this->inputs->set($name, $input);
        return $this;
    }
    
    /**
     *
     * @param string $name
     * @return \matpoppl\InputFilter\InputContainer
     */
    public function remove(string $name)
    {
        $this->inputs->remove($name);
        return $this;
    }
    
    public function getValue()
    {
        $ret = [];
        
        foreach ($this->inputs->getNames() as $key) {
            $ret[$key] = $this->inputs->get($key)->getValue();
        }
        
        return $ret;
    }
    
    public function setValue($value)
    {
        if (! is_array($value)) {
            $value = [];
        }
        
        foreach ($this->inputs->getNames() as $key) {
            $val = array_key_exists($key, $value) ? $value[$key] : null;
            $this->inputs->get($key)->setValue($val);
        }
        
        return $this;
    }
    
    public function isValid($ctx = null) : bool
    {
        $isValid = true;
        
        foreach ($this->inputs->getNames() as $key) {
            if (! $this->inputs->get($key)->isValid($ctx)) {
                $isValid = false;
            }
        }
        
        return $isValid;
    }
    
    public function getMessages() : array
    {
        $ret = [];
        
        foreach ($this->inputs->getNames() as $key) {
            $ret[$key] = $this->inputs->get($key)->getMessages();
        }
        
        return $ret;
    }
    
    public function getTranslatedMessages(TranslatorInterface $translator) : array
    {
        $ret = [];
        
        foreach ($this->inputs->getNames() as $key) {
            $ret[$key] = $this->inputs->get($key)->getTranslatedMessages($translator);
        }
        
        return $ret;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
}
