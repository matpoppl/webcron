<?php

namespace matpoppl\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface
{
    private $options;
    
    public function __construct(array $options = null)
    {
        $this->options = $options ?: [];
    }
    
    public function getAdapter()
    {
        return new Adapter\StreamAdapter($this->options);
    }
    
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->getAdapter()->sendRequest($request);
    }
    
    public function withOptions(array $options, $merge = false): ClientInterface
    {
        $ret = clone $this;
        if ($merge) {
            $ret->options = array_merge($ret->options, $options);
        } else {
            $ret->options = $options;
        }
        return $ret;
    }
}
