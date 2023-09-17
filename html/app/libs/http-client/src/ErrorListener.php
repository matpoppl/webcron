<?php

namespace matpoppl\HttpClient;

class ErrorListener
{
    private $errors = [];
    
    public function __construct()
    {
        $self =& $this;
        
        set_error_handler(function($t, $m, $f, $l) use ($self) {
            $self->errors[] = [
                'type' => $t,
                'message' => $m,
                'file' => $f,
                'line' => $l,
            ];
        }, -1);
    }
    
    /**
     * 
     * @param bool $throwException
     * @throws Exception
     * @return NULL|Exception
     */
    public function stop($throwException)
    {
        restore_error_handler();
        
        $ex = null;
        foreach ($this->errors as $error) {
            if (null === $ex) {
                $ex = new Exception($error['message'], $error['type']);
            } else {
                $ex = new Exception($error['message'], $error['type'], $ex);
            }
        }
        
        if ($throwException && null !== $ex) {
            throw $ex;
        }
        
        return $ex;
    }
}
