<?php

namespace matpoppl\DataValidator;

class NumberBetweenValidator implements ValidatorInterface
{
    /** @val int */
    private $min = null;
    private $max = null;
    /** @val string */
    private $encoding;
    
    public function __construct(array $options)
    {
        $this->min = $options['min'] ?? null;
        $this->max = $options['max'] ?? null;
    }
    
    public function __invoke($data, $ctx = null)
    {
        if (null === $data) {
            $data = '';
        }
        
        if (null !== $this->min && $data < $this->min) {
            return [['Expecting at least {n} characters', ['{n}' => $this->min], 'data_validator.NumberBetween']];
        }
        
        if (null !== $this->max && $data > $this->max) {
            return [['Expecting at most {n} characters', ['{n}' => $this->max], 'data_validator.NumberBetween']];
        }
        
        return false;
    }
}
