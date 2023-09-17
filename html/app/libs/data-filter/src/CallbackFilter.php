<?php

namespace matpoppl\DataFilter;

class CallbackFilter implements FilterInterface
{
    /** @val Callable */
    private $callback;
    
    public function __construct(array $options)
    {
        $this->callback = $options['callback'] ?? null;
        
        if (! is_callable($this->callback)) {
            throw new \UnexpectedValueException('Callable required');
        }
    }
    
    public function __invoke($data)
    {
        return call_user_func($this->callback, $data);
    }
}
