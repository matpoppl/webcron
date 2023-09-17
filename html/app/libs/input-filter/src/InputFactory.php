<?php

namespace matpoppl\InputFilter;

use matpoppl\ServiceManager\ServiceManagerInterface;

class InputFactory
{
    /** @var ServiceManagerInterface */
    private $sm;
    
    public function __construct(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
    }
    
    public function create(array $options)
    {
        if (array_key_exists('type', $options)) {
            $className = $options['type'];
            unset($options['type']);
        } else if (array_key_exists('inputs', $options)) {
            $className = InputFilter::class;
        } else {
            $className = Input::class;
        }
        
        if ($this->sm->has($className)) {
            return $this->sm->create($className, $options);
        }
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException('Input class dont exists `'.$className.'`');
        }
        
        if (array_key_exists('filters', $options)) {
            $options['filters'] = $this->sm->create('data.filter.builder')->createFilterChain($options['filters']);
        }
        
        if (array_key_exists('validators', $options)) {
            $options['validators'] = $this->sm->create('data.validator.builder')->createValidatorChain($options['validators']);
        }
        
        if ($className === InputFilter::class) {
            return new $className($this->sm, $options);
        }
        
        return new $className($options);
    }
}
