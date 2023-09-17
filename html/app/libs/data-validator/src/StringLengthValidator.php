<?php

namespace matpoppl\DataValidator;

class StringLengthValidator implements ValidatorInterface
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
        $this->encoding = $options['encoding'] ?? 'UTF-8';
    }
    
    public function __invoke($data, $ctx = null)
    {
        if (null === $data) {
            $data = '';
        }
        
        if (! is_string($data)) {
            throw new \UnexpectedValueException('Only string are supported');
        }
        
        $len = mb_strlen($data);
        
        if (null !== $this->min && $len < $this->min) {
            return [
                ['Expecting at least {n} characters', ['{n}' => $this->min], 'data_validator.StringLength']
            ];
        }
        
        if (null !== $this->max && $len > $this->max) {
            return [
                ['Expecting at most {n} characters', ['{n}' => $this->max], 'data_validator.StringLength']
            ];
        }
        
        return false;
    }
}
