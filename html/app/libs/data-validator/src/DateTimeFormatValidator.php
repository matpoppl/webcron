<?php

namespace matpoppl\DataValidator;

class DateTimeFormatValidator implements ValidatorInterface
{
    /** @val string */
    private $format;
    
    public function __construct(array $options)
    {
        $this->format = $options['format'] ?? null;
    }
    
    public function __invoke($data, $ctx = null)
    {
        $ts = strtotime($data);
        
        if (false === $ts) {
            return [['DateTime is unsupported by `strtotime`', null, 'data_validator.DateTimeFormat']];
        }
        
        $expected = date($this->format, $ts);
        
        if ($expected === $data) {
            return false;
        }
        
        return [['Date doesn\'t match format `{f}` `{s}`', ['{f}' => $this->format, '{s}' => $data], 'data_validator.DateTimeFormat']];
    }
}
