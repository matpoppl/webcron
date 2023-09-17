<?php

namespace matpoppl\DataValidator;

class ValidatorChain implements ValidatorInterface
{
    /** @val ValidatorInterface[] */
    private $validators;
    
    public function __construct(array $options)
    {
        $this->validators = $options['validators'] ?? [];
    }
    
    public function __invoke($data, $ctx = null)
    {
        foreach ($this->validators as $validator) {
            $hasErrors = $validator($data, $ctx);
            if (false !== $hasErrors) {
                return $hasErrors;
            }
        }
        
        return false;
    }
}
