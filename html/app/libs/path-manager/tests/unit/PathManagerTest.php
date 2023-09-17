<?php

namespace matpoppl\PathManager;

use PHPUnit\Framework\TestCase;

class PathManagerTest extends TestCase
{
    public function testResolve()
    {
        $pm = new PathManager([
            'locations' => [
                'foo' => '/',
                'bar' => 'foo:/bar1/bar2/',
                'baz' => 'bar:baz1/baz2',
            ],
        ]);
        
        self::assertEquals('/file.ext', $pm->getPathname('foo:file.ext'));
        self::assertEquals('/bar1/bar2/dir/file.ext', $pm->getPathname('bar:dir/file.ext'));
        self::assertEquals('/bar1/bar2/baz1/baz2/dir/subdir/file.ext', $pm->getPathname('baz:/dir/subdir/file.ext'));
    }
}
