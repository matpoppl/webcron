<?php

namespace matpoppl\SmallMVC\Router;

use matpoppl\SmallMVC\Message\RequestInterface;

class MatchException extends \RuntimeException
{
    private $request;
    
    public function __construct(RequestInterface $request, $message, $code, $previous = null)
    {
        $this->request = $request;
        parent::__construct ($message, $code, $previous);
    }
 
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }
}
