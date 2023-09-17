<?php

namespace matpoppl\HttpMessage;

use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testFull()
    {
        $expected = 'aaa://bbb:ccc@ddd:555/fff/ggg.hhh?iii=jjj&hhh=iii#jjj';
        
        $uri = new Uri($expected);
        
        self::assertEquals('aaa', $uri->getScheme());
        self::assertEquals('bbb:ccc', $uri->getUserInfo());
        self::assertEquals('bbb:ccc@ddd:555', $uri->getAuthority());
        self::assertEquals('ddd', $uri->getHost());
        self::assertEquals(555, $uri->getPort());
        self::assertEquals('/fff/ggg.hhh', $uri->getPath());
        self::assertEquals('iii=jjj&hhh=iii', $uri->getQuery());
        self::assertEquals('jjj', $uri->getFragment());
        
        self::assertEquals($expected, '' . $uri);
    }
}
