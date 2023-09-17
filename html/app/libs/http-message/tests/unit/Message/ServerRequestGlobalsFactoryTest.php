<?php

namespace matpoppl\HttpMessage;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestGlobalsFactoryTest extends TestCase
{
    public function testHttp()
    {
        $_COOKIE['foocookie'] = 'barcookie';
        
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/dir/file.ext?foo=1&bar=2#/foo/bar';
        $_SERVER['SERVER_PROTOCOL'] = '1.1';
        $_SERVER['HTTP_ACCEPT'] = '*/*';
        
        $factory = new ServerRequestGlobalsFactory();
        /** @var ServerRequestInterface $req */
        $req = $factory($this->createMock(ContainerInterface::class), ServerRequest::class);
        
        self::assertTrue($req instanceof ServerRequestInterface);
        
        self::assertEquals(null, $req->getAttribute('missing'), 'getAttribute()');
        self::assertEquals([], $req->getAttributes(), 'getAttributes()');
        self::assertEquals(['foocookie' => 'barcookie'], $req->getCookieParams(), 'getCookieParams()');
        self::assertEquals('*/*', $req->getHeaderLine('Accept'), 'getHeaderLine()');
        self::assertEquals(['*/*'], $req->getHeader('Accept'), 'getHeader()');
        self::assertEquals(['localhost'], $req->getHeader('Host'), 'getHeader()');
        self::assertEquals(['accept' => ['*/*'], 'host' => ['localhost']], $req->getHeaders(), 'getHeaders()');
        self::assertEquals('POST', $req->getMethod(), 'getMethod()');
        self::assertEquals(null, $req->getParsedBody(), 'getParsedBody()');
        self::assertEquals('', $req->getBody()->getContents(), 'getContents()');
        self::assertEquals('1.1', $req->getProtocolVersion(), 'getProtocolVersion()');
        self::assertEquals(['foo' => 1, 'bar' => 2], $req->getQueryParams(), 'getQueryParams()');
        self::assertEquals('http://localhost/dir/file.ext?foo=1&bar=2', $req->getRequestTarget(), 'getRequestTarget()');
        //self::assertEquals('', $req->getServerParams(), 'getServerParams()');
        self::assertEquals([], $req->getUploadedFiles(), 'getUploadedFiles()');
        self::assertEquals('http://localhost/dir/file.ext?foo=1&bar=2#/foo/bar', ''.$req->getUri(), 'getUri()');
    }
    
    public function testJson()
    {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SERVER_PROTOCOL'] = '1.1';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json; charset=UTF-8';
        
        $expected = ['foo' => 11, 'bar' => [22,333]];
        
        $factory = new ServerRequestGlobalsFactory();
        /** @var ServerRequestInterface $req */
        $req = $factory($this->createMock(ContainerInterface::class), ServerRequest::class);
        
        $req = $req->withBody(InMemoryStream::fromString(json_encode($expected)));
        
        self::assertTrue($req instanceof ServerRequestInterface);
        
        self::assertEquals($expected, $req->getParsedBody(), 'getParsedBody()');
    }
}
