<?php

namespace matpoppl\HttpMessage;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string[]|NULL */
    private $parts = null;
    /** @var string[] */
    private $queryParams = null;
    /** @var string|NULL */
    private $uri = null;
    
    public function __construct($uri = null)
    {
        $this->uri = $uri;
    }
    
    public function getPart($name, $default = null)
    {
        if (null === $this->parts) {
            
            $this->parts = array(
                'scheme' => null,
                'host' => null,
                'port' => null,
                'user' => null,
                'pass' => null,
                'path' => null,
                'query' => null,
                'fragment' => null
            );
            
            $tmp = (null === $this->uri) ? array() : parse_url($this->uri);
            
            if (! is_array($tmp)) {
                throw new \InvalidArgumentException('URI parse error');
            }
            
            $this->parts = array_merge($this->parts, $tmp);
            
        }
        return array_key_exists($name, $this->parts) ? $this->parts[$name] : $default;
    }
    
    public function setPart($name, $value)
    {
        if (null === $this->parts) {
            $this->getPart($name);
        }
        $this->parts[$name] = $value;
        return $this;
    }
    
    public function getScheme()
    {
        return $this->getPart('scheme', '');
    }

    public function getUserInfo()
    {
        $ret = $this->getPart('user', '');
        $pwd = $this->getPart('pass', '');
        if (strlen($pwd) > 0) {
            $ret .= ':' . $pwd;
        }
        return $ret;
    }

    public function getAuthority()
    {
        $ret = $this->getUserInfo();

        if (strlen($ret) > 0) {
            $ret .= '@';
        }

        $ret .= $this->getPart('host', '');

        $port = $this->getPort();

        if ($port > 0) {
            $ret .= ':' . $port;
        }

        return $ret;
    }

    public function getHost()
    {
        return $this->getPart('host', '');
    }

    public function getPort()
    {
        $port = $this->getPart('port');
        switch ($this->getScheme() . $port) {
            case 'http':
            case 'http80':
            case 'https':
            case 'https443':
                return null;
        }
        
        return (int) $port;
    }

    public function getPath()
    {
        return $this->getPart('path', '');
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Psr\Http\Message\UriInterface::getQuery()
     */
    public function getQuery()
    {
        return $this->getPart('query', '');
    }
    
    /** @return string[] */
    public function getQueryParams()
    {
        if (null === $this->queryParams) {
            parse_str($this->getQuery(), $this->queryParams);
        }
        return $this->queryParams;
    }

    public function getFragment()
    {
        return $this->getPart('fragment', '');
    }

    public function __toString()
    {
        if (null === $this->parts) {
            return '' . $this->uri;
        }

        $ret = $this->getScheme();

        if (strlen($ret) > 0) {
            $ret .= '://';
        }

        $ret .= $this->getAuthority();
        $ret .= $this->getPath() ?: '/';
        $query = $this->getQuery();

        if (strlen($query) > 0) {
            $ret .= '?' . $query;
        }

        $fragment = $this->getFragment();

        if (strlen($fragment) > 0) {
            $ret .= '#' . $fragment;
        }

        return $ret;
    }

    public function withScheme($scheme)
    {
        $ret = clone $this;
        $ret->setPart('scheme', $scheme);
        return $ret;
    }

    public function withUserInfo($user, $password = null)
    {
        $ret = clone $this;
        $ret->setPart('user', $user);
        $ret->setPart('pass', $password);
        return $ret;
    }

    public function withHost($host)
    {
        $ret = clone $this;
        $ret->setPart('host', $host);
        return $ret;
    }

    public function withPort($port)
    {
        $ret = clone $this;
        $ret->setPart('port', $port);
        return $ret;
    }

    public function withPath($path)
    {
        $ret = clone $this;
        $ret->setPart('path', $path);
        return $ret;
    }

    public function withQuery($query)
    {
        parse_str($query, $query);
        
        if (! is_array($query)) {
            throw new \UnexpectedValueException('Invalid query format');
        }
        
        $query = http_build_query($query);
        
        $ret = clone $this;
        $ret->queryParams = null;
        $ret->setPart('query', $query);
        return $ret;
    }
    
    public function withFragment($fragment)
    {
        $ret = clone $this;
        $ret->setPart('fragment', $fragment);
        return $ret;
    }
}
