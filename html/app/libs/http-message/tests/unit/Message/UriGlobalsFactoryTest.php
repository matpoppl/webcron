<?php

namespace matpoppl\HttpMessage;

use PHPUnit\Framework\TestCase;

class UriGlobalsFactoryTest extends TestCase
{
    public function testHttp()
    {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/dir/file.ext?foo=1&bar=2#/foo/bar';
        
        $factory = new UriGlobalsFactory();
        
        $uri = $factory->createFromGlobals();
        
        self::assertEquals('http', $uri->getScheme(), 'getScheme()');
        self::assertEquals(null, $uri->getUserInfo(), 'getUserInfo()');
        self::assertEquals('localhost', $uri->getHost(), 'getHost()');
        self::assertEquals(null, $uri->getPort(), 'getPort()');
        self::assertEquals('localhost', $uri->getAuthority(), 'getAuthority()');
        self::assertEquals('/dir/file.ext', $uri->getPath(), 'getPath()');
        self::assertEquals('foo=1&bar=2', $uri->getQuery(), 'getQuery()');
        self::assertEquals('/foo/bar', $uri->getFragment(), 'getFragment()');
        
        self::assertEquals('http://localhost/dir/file.ext?foo=1&bar=2#/foo/bar', ''.$uri->__toString(), '__toString()');
    }
    
    public function testCustom()
    {
        $_SERVER['REQUEST_SCHEME'] = 'foo';
        $_SERVER['PHP_AUTH_USER'] = 'pwuser:pwpass';
        $_SERVER['SERVER_NAME'] = 'hostname';
        $_SERVER['SERVER_PORT'] = 8123;
        $_SERVER['REQUEST_URI'] = '/dir/file.ext?foo=1&bar=2#/foo/bar';
        
        $factory = new UriGlobalsFactory();
        
        $uri = $factory->createFromGlobals();
        
        self::assertEquals('foo', $uri->getScheme(), 'getScheme()');
        self::assertEquals('pwuser:pwpass', $uri->getUserInfo(), 'getUserInfo()');
        self::assertEquals('hostname', $uri->getHost(), 'getHost()');
        self::assertEquals(8123, $uri->getPort(), 'getPort()');
        self::assertEquals('pwuser:pwpass@hostname:8123', $uri->getAuthority(), 'getAuthority()');
        self::assertEquals('/dir/file.ext', $uri->getPath(), 'getPath()');
        self::assertEquals('foo=1&bar=2', $uri->getQuery(), 'getQuery()');
        self::assertEquals('/foo/bar', $uri->getFragment(), 'getFragment()');
        
        self::assertEquals('foo://pwuser:pwpass@hostname:8123/dir/file.ext?foo=1&bar=2#/foo/bar', ''.$uri->__toString(), '__toString()');
    }
}
