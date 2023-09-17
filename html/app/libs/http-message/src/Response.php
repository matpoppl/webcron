<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response extends Message implements ResponseInterface
{
    private $statusCode;
    private $reasonPhrase;

    public function withStatus($code, $reasonPhrase = '')
    {
        $ret = clone $this;
        $ret->statusCode = $code;
        $ret->reasonPhrase = $reasonPhrase;
        return $ret;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function send()
    {
        if (! headers_sent()) {
            $code = $this->getStatusCode();

            if ($code > 0) {
                http_response_code($code);
            }

            foreach (array_keys($this->getHeaders()) as $name) {
                header($name . ': ' . $this->getHeaderLine($name));
            }
        }
        
        $body = $this->getBody();
        
        if ($body instanceof StreamInterface) {
            echo $body->getContents();
        }
    }
    
    /**
     *
     * @param string $response
     * @return \matpoppl\HttpMessage\ServerRequest
     */
    public static function fromString(string $response)
    {
        $end = strpos($response, "\r\n\r\n");
        
        $headers = false === $end ? $response : substr($response, 0, $end);
        $body = false === $end ? null : InMemoryStream::fromString(substr($response, $end + 4));
        
        $extra = null;
        $headers = Headers::createFromResponseString($response, $extra);
        
        $protocolVersion = null;
        if (is_array($extra)) {
            $protocolVersion = $extra['version'];
        }
        
        $ret = new static($headers, $body, $protocolVersion);
        
        if (is_array($extra)) {
            $ret->reasonPhrase = $extra['reason'];
            $ret->statusCode = (int) $extra['code'];
        }
        
        return $ret;
    }
}
