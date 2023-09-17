<?php

namespace matpoppl\HttpClient;

class Exception extends \RuntimeException
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
