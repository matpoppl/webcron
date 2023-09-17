<?php

namespace matpoppl\Mailer\Transport;

class TransportException extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 
     * @param resource $socket
     * @param mixed $previous
     * @return static
     */
    public static function fromLastError($socket = null, $previous = null)
    {
        $code = socket_last_error($socket);
        $msg = socket_strerror($code);
        return new static($msg, $code, $previous);
    }
}
