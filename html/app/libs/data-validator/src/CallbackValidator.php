<?php

namespace matpoppl\DataValidator;

function callback_has_errors_api($value, $ctx, CallbackValidator $validator)
{}

class CallbackValidator implements ValidatorInterface
{
    /** @val Callable See callback_api() */
    private $callback;
    /** @val string */
    private $errorMessage;
    
    public function __construct(array $options)
    {
        $this->callback = $options['callback'] ?? null;
        $this->errorMessage = $options['error_message'] ?? null;
        
        if (! is_callable($this->callback)) {
            throw new \UnexpectedValueException('Callable required');
        }
    }
    
    public function __invoke($data, $ctx = null)
    {
        $status = call_user_func($this->callback, $data, $ctx, $this);
        
        if (false === $status) {
            return $status;
        }
        
        if (null !== $this->errorMessage) {
            return [[$this->errorMessage, $data]];
        }
        
        if (is_string($status)) {
            return [[$status, $data]];
        }
        
        if (is_array($status)) {
            return $status;
        } 
        
        return ['Invalid value'];
    }
}
