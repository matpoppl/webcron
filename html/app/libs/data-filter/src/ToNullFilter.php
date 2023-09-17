<?php

namespace matpoppl\DataFilter;

class ToNullFilter implements FilterInterface
{
    const TYPE_ANY = 'any';
    const TYPE_STRING = 'string';
    const TYPE_COUNT = 'count';
    
    private $type;
    
    public function __construct(array $options = null)
    {
        if (null === $options) {
            return;
        }
        
        $this->type = $options['type'] ?? self::TYPE_ANY;
    }
    
    public function __invoke($data)
    {
        switch ($this->type) {
            case self::TYPE_STRING:
                $len = is_scalar($data) || (is_object($data) && method_exists($data, '__toString')) ? strlen(''.$data) : 0;
                return $len > 0 ? $data : null;
            case self::TYPE_COUNT:
                $count = (is_array($data) || $data instanceof \Countable) ? count($data) : 0;
                return $count > 0 ? $data : null;
        }
        
        return empty($data) ? null : $data;
    }
}
