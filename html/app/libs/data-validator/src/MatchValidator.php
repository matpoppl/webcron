<?php

namespace matpoppl\DataValidator;

class MatchValidator implements ValidatorInterface
{
    const COMPARE_LOOSE = 'loose';
    const COMPARE_STRICT = 'strict';
    
    private $name;
    private $compare;
    
    public function __construct(array $options)
    {
        if (! array_key_exists('name', $options)) {
            throw new \UnexpectedValueException('`name` option required');
        }
        
        $this->name = $options['name'];
        $this->compare = $options['compare'] ?? self::COMPARE_STRICT;
    }
    
    public function __invoke($data, $ctx = null)
    {
        if (! isset($ctx[$this->name])) {
            return [['Missing `{s}` in context', ['{s}' => $this->name], 'data_validator.Match']];
        }
        
        $isValid = (self::COMPARE_STRICT === $this->compare) ? ($data === $ctx[$this->name]) : ($data == $ctx[$this->name]);

        if ($isValid) {
            return false;
        }
        
        return [['Value doesn\'t match `{s}`', ['{s}' => $this->name], 'data_validator.Match']];
    }
}
