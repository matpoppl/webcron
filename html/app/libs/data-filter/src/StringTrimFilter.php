<?php

namespace matpoppl\DataFilter;

class StringTrimFilter implements FilterInterface
{
    const MODE_LEFT = 'ltrim';
    const MODE_RIGHT = 'rtrim';
    const MODE_BOTH = 'trim';
    
    private $mode = self::MODE_BOTH;
    private $mask = null;
    
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(array $options)
    {
        $this->mode = $options['mode'] ?? $this->mode;
        $this->mask = $options['mask'] ?? $this->mask;
        return $this;
    }
    
    public function __invoke($data)
    {
        switch ($this->mode) {
            case self::MODE_LEFT: return (null === $this->mask) ? ltrim($data) : ltrim($data, $this->mask);
            case self::MODE_RIGHT: return (null === $this->mask) ? rtrim($data) : rtrim($data, $this->mask);
        }

        return (null === $this->mask) ? trim($data) : trim($data, $this->mask);
    }
}
