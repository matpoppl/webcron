<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    /** @var string */
    private $protocolVersion;
    /** @var HeadersInterface */
    private $headers;
    /** @var StreamInterface */
    private $body;
    
    public function __construct($headers = null, StreamInterface $body = null, $protocolVersion = null)
    {
        $this->protocolVersion = $protocolVersion;
        $this->body = $body;

        if (null === $headers) {
            $this->headers = new Headers();
        } else if (is_array($headers)) {
            $this->headers = new Headers($headers);
        } else if ($headers instanceof HeadersInterface) {
            $this->headers = $headers;
        } else {
            throw new \UnexpectedValueException('Unsupported headers type');
        }
    }
    
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }
    
    public function getHeader($name)
    {
        return $this->headers->get($name);
    }
    
    public function getHeaderLine($name)
    {
        return $this->headers->getLine($name);
    }
    
    public function getHeaders()
    {
        return $this->headers->toArray();
    }
    
    /** @return HeadersInterface */
    public function getHeadersObject()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withProtocolVersion($version)
    {
        $ret = clone $this;
        $ret->protocolVersion = $version;
        return $ret;
    }

    public function withAddedHeader($name, $value)
    {
        $ret = clone $this;
        $ret->headers = $ret->headers->withAdded($name, $value);
        return $ret;
    }

    public function withoutHeader($name)
    {
        $ret = clone $this;
        $ret->headers = $ret->headers->without($name);
        return $ret;
    }
    
    public function withHeader($name, $value)
    {
        $ret = clone $this;
        $ret->headers = $ret->headers->with($name, $value);
        return $ret;
    }
    
    public function withHeaders(array $headers)
    {
        $ret = clone $this;
        $ret->headers = $ret->headers->withArray($headers);
        return $ret;
    }

    public function withBody(StreamInterface $body)
    {
        $ret = clone $this;
        $ret->body = $body;
        return $ret;
    }
}
