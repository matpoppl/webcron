<?php

namespace matpoppl\DataValidator;

use matpoppl\ServiceManager\ServiceManagerInterface;

class ValidatorBuilder
{
    /** @var ServiceManagerInterface */
    private $sm;
    
    public function __construct(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
    }
    
    public function createValidatorChain(array $options)
    {
        $options = array_key_exists('validators', $options) ? $options : ['validators' => $options];
        
        foreach ($options['validators'] as $i => $validator) {
            $options['validators'][$i] = $this->createValidator($validator);
        }
        
        return new ValidatorChain($options);
    }
    
    public function createValidator($options)
    {
        if (is_string($options)) {
            $type = $options;
            $options = null;
        } else if (is_array($options)) {
            
            if (array_key_exists('type', $options)) {
                $type = $options['type'];
                $options = $options['options'] ?? null;
            } else {
                $type = array_shift($options);
                $options = array_shift($options) ?: [];
            }
            
        } else {
            throw new \UnexpectedValueException('Unsupported filter options type');
        }
        
        if ($this->sm->has($type)) {
            return $this->sm->create($type, $options);
        }
        
        $type = 'data.validator.' . $type;
        
        if (! $this->sm->has($type)) {
            throw new \UnexpectedValueException('Validator dont exists `'.$type.'`');
        }
        
        return $this->sm->create($type, $options);
    }
}
