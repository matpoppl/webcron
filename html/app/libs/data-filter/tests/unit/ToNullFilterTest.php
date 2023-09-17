<?php

namespace matpoppl\DataFilter;

use PHPUnit\Framework\TestCase;

class ToNullFilterTest extends TestCase
{
    public function testAny()
    {
        $filter = new ToNullFilter();
        
        self::assertEquals(1, $filter(1));
        self::assertEquals(true, $filter(true));
        self::assertEquals('a', $filter('a'));
        self::assertEquals(null, $filter(false));
        self::assertEquals(null, $filter(null));
        self::assertEquals(null, $filter(''));
        self::assertEquals(null, $filter(0));
        self::assertEquals(null, $filter([]));
    }
    
    public function testString()
    {
        $filter = new ToNullFilter(['type' => ToNullFilter::TYPE_STRING]);
        
        self::assertEquals(1, $filter(1));
        self::assertEquals(true, $filter(true));
        self::assertEquals('a', $filter('a'));
        self::assertEquals(null, $filter(false));
        self::assertEquals(null, $filter(null));
        self::assertEquals(null, $filter(''));
        self::assertEquals(0, $filter(0));
    }
    
    public function testCountable()
    {
        $filter = new ToNullFilter(['type' => ToNullFilter::TYPE_COUNT]);
        
        self::assertEquals(null, $filter([]));
        self::assertEquals([1], $filter([1]));
    }
}
