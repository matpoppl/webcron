<?php

namespace matpoppl\PathManager;

use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testAppend()
    {
        $loc = new Location('/foo/');
        
        self::assertEquals('/foo/bar', $loc->append('bar')->getPathname());
        self::assertEquals('/foo/bar', $loc->append('/bar')->getPathname());
        self::assertEquals('/foo/bar', $loc->append('/bar/')->getPathname());
        
        self::assertEquals('/foo/bar/baz/qux', $loc->append('/bar/')->append('/baz/')->append('/qux/')->getPathname());
    }
}
