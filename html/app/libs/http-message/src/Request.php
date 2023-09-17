<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

class Request extends Message implements RequestInterface
{
    /** @var string */
    private $method;
    /** @var string */
    private $requestTarget = null;
    /** @var UriInterface */
    private $uri;

    public function getMethod()
    {
        return $this->method;
    }

    public function getRequestTarget()
    {
        if (null === $this->requestTarget) {
            $uri = '' . $this->getUri();
            $pos = strpos($uri, '#');
            $this->requestTarget = $pos > 0 ? substr($uri, 0, $pos) : $uri;
        }
        
        return $this->requestTarget;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withRequestTarget($requestTarget)
    {
        $ret = clone $this;
        $ret->requestTarget = $requestTarget;
        return $ret;
    }

    public function withMethod($method)
    {
        $ret = clone $this;
        $ret->method = $method;
        return $ret;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::withUri()
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $ret = clone $this;
        $ret->uri = $uri;
        
        $newHost = $uri->getHost();
        $oldHost = $ret->getHeaderLine('Host');
        
        // new exists, old empty, preserve old
        if ($newHost && (! $oldHost || ! $preserveHost)) {
            return $ret->withHeader('Host', $newHost);
        }
        
        return $ret;
    }
    
    /**
     * 
     * @param string $method
     * @param string|Uri $url
     * @param array|Headers|NULL $headers
     * @param string|\Psr\Http\Message\StreamInterface|NULL $body
     * @return \matpoppl\HttpMessage\Request
     */
    public static function createFromArgs($method, $url, $headers = null, $body = null)
    {
        $body = is_string($body) ? InMemoryStream::fromString($body) : $body;
        $req = new static($headers, $body);
        $req->uri = is_string($body) ? new Uri($url) : $url;
        $req->method = strtoupper($method);
        return $req;
    }
}
