<?php

namespace matpoppl\HttpMessage;

class Headers implements HeadersInterface
{
    /** @var string[][] */
    private $headers = array();
    
    public function __construct(array $headers = null)
    {
        if (null !== $headers) {
            $this->setHeaders($headers);
        }
    }
    
    public function has($name)
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->headers);
    }
    
    public function get($name)
    {
        $name = strtolower($name);
        if (! $this->has($name)) {
            throw new \DomainException('Header dont exists');
        }
        return $this->headers[$name];
    }
    
    public function getLine($name)
    {
        if ($this->has($name)) {
            $header = $this->get($name);
            return implode(',', $header);
        }
        return '';
    }
    
    public function add($name, $value)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->headers)) {
            if (is_array($value)) {
                $this->headers[$name] = array_merge($this->headers[$name], $value);
            } else {
                $this->headers[$name][] = $value;
            }
        } else {
            $this->headers[$name] = is_array($value) ? $value : array($value);
        }
        
        return $this;
    }
    
    public function set($name, $value)
    {
        return $this->remove($name)->add($name, $value);
    }
    
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->headers[$name]);
        }
        return $this;
    }
    
    public function toString()
    {
        $ret = '';
        
        foreach (array_keys($this->headers) as $name) {
            $ret .= $name . ':' . $this->getLine($name) . "\r\n";
        }
        
        return $ret;
    }
    
    public function toArray()
    {
        return $this->headers;
    }
    
    public function setHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }
        return $this;
    }
    
    public function withAdded($name, $value)
    {
        $ret = clone $this;
        $ret->add($name, $value);
        return $ret;
    }
    
    public function without($name)
    {
        $ret = clone $this;
        $ret->remove($name);
        return $ret;
    }
    
    public function with($name, $value)
    {
        $ret = clone $this;
        $ret->set($name, $value);
        return $ret;
    }
    
    public function withArray(array $headers)
    {
        $ret = clone $this;
        $ret->setHeaders($headers);
        return $ret;
    }
    
    public static function createFromGlobals()
    {
        $ret = [];
        
        foreach (array_keys($_SERVER) as $key) {
            if(0 === strpos($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $ret[$name] = $_SERVER[$key];
            }
        }
        
        return new static($ret);
    }
    
    public static function createFromResponseString($response, &$extra = null)
    {
        $ret = [];
        $end = strpos($response, "\r\n\r\n");
        $response = false === $end ? $response : substr($response, 0, $end);
        foreach (explode("\r\n", $response) as $line) {
            $matched = null;
            if (preg_match('#([^:]+):\s?(.*)#', $line, $matched) > 0) {
                $ret[ $matched[1] ] = [ $matched[2] ];
            } else if (preg_match('#^HTTP/(\d+(\.\d+)?) (\d+) (.*)#', $line, $matched) > 0) {
                $extra = [
                    'version' => $matched[1],
                    'code' => $matched[3],
                    'reason' => $matched[4],
                ];
            }
        }
        
        return new static($ret);
    }
}
