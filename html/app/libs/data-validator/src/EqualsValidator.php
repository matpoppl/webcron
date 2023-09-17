<?php

namespace matpoppl\DataValidator;

class EqualsValidator implements ValidatorInterface
{
    const COMPARE_LOOSE = 'loose';
    const COMPARE_STRICT = 'strict';
    
    private $expected;
    private $compare;
    private $mask;
    
    public function __construct(array $options)
    {
        if (! array_key_exists('expected', $options)) {
            throw new \UnexpectedValueException('`expected` value required');
        }
        
        $this->expected = $options['expected'];
        $this->compare = $options['compare'] ?? self::COMPARE_STRICT;
        $this->mask = $options['mask'] ?? true;
    }
    
    public function __invoke($data, $ctx = null)
    {
        $isValid = (self::COMPARE_STRICT === $this->compare) ? ($this->expected === $data) : ($this->expected == $data);

        if ($isValid) {
            return false;
        }
        
        if ($this->mask) {
            $data = str_repeat('*', strlen($this->expected));
        }
        
        return [['Must equal {s}', ['{s}' => $data], 'data_validator.Equals']];
    }
}
