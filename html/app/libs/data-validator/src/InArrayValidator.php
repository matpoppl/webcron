<?php

namespace matpoppl\DataValidator;

class InArrayValidator implements ValidatorInterface
{
    /** @val array */
    private $haystack;
    /** @val boolean */
    private $strict;
    
    public function __construct(array $options)
    {
        $this->haystack = $options['haystack'];
        $this->strict = $options['strict'] ?? true;
    }
    
    public function __invoke($data, $ctx = null)
    {
        if (! in_array($data, $this->haystack, $this->strict)) {
            return [['Needle `{needle}` not found in haystack `{haystack}`', ['{needle}' => $data, '{haystack}' => json_encode($this->haystack)], 'validator']];
        }
        
        return false;
    }
}
