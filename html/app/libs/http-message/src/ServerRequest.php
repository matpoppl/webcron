<?php
namespace matpoppl\HttpMessage;

use Psr\Http\Message\StreamInterface;
use matpoppl\HttpMessage\ParsedBody\AbstractData;

class ServerRequest extends Request implements ServerRequestInterface
{
    private $attributes = [];
    private $cookies = [];
    private $serverParams = [];
    private $uploadedFiles = [];
    private $parsedBody = null;
    private $parsedBodyFactory = null;
    
    public function __construct($headers = null, StreamInterface $body = null, $protocolVersion = null)
    {
        parent::__construct($headers, $body, $protocolVersion);
    }
    
    public function has(string $id) : bool
    {
        return array_key_exists($id, $this->attributes);
    }
    
    public function get(string $id, $default = null)
    {
        return array_key_exists($id, $this->attributes) ? $this->attributes[$id] : $default;
    }
    
    public function set(string $id, $value)
    {
        $this->attributes[$id] = $value;
        return $this;
    }
    
    public function remove(string $id)
    {
        if ($this->has($id)) {
            unset($this->attributes[$id]);
        }
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->attributes = $params;
        return $this;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $ret = clone $this;
        $ret->attributes[$name] = $value;
        return $ret;
    }

    public function withoutAttribute($name)
    {
        $ret = clone $this;
        if (array_key_exists($name, $ret->attributes)) {
            unset($ret->attributes[$name]);
        }
        return $ret;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies)
    {
        $ret = clone $this;
        $ret->cookies = $cookies;
        return $ret;
    }
    
    public function setParsedBodyFactory($factory)
    {
        $this->parsedBodyFactory = $factory;
        return $this;
    }
    
    public function getParsedBody()
    {
        if (null === $this->parsedBody && $this->parsedBodyFactory) {
            $this->parsedBody = $this->parsedBodyFactory->createFrom($this);
        }
        
        if ($this->parsedBody instanceof AbstractData) {
            return count($this->parsedBody) > 0 ? $this->parsedBody->getArrayCopy() : null;
        }
        
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        $ret = clone $this;
        $ret->parsedBody = $data;
        return $ret;
    }

    public function getQueryParams()
    {
        $uri = $this->getUri();
        
        if ($uri instanceof Uri) {
            return $uri->getQueryParams();
        }
        
        $query = $uri->getQuery();
        $params = [];
        
        if (strlen($query) > 0) {
            parse_str($query, $params);
        }
        
        return $params;
    }

    public function withQueryParams(array $query)
    {
        $query = http_build_query($query);
        $uri = $this->getUri()->withQuery($query);
        return $this->withUri($uri);
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $ret = clone $this;
        $ret->uploadedFiles = $uploadedFiles;
        return $ret;
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }
}
