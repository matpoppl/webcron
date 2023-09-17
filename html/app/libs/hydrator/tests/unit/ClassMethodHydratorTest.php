<?php

namespace matpoppl\Hydrator;

use PHPUnit\Framework\TestCase;

class ClassMethodHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $obj = new SampleMethodClass();
        
        self::assertEquals(null, $obj->getFoo());
        self::assertEquals(null, $obj->getBarBaz());
        self::assertEquals(null, $obj->getQUX());
        self::assertEquals(null, $obj->getXYZy());
        
        $factory = new HydratorFactory();
        $hydrator = $factory->create([
            'type' => 'ClassMethod',
            'options' => [
                'setterNamingStrategy' => 'Underscore2CamelCase',
            ],
        ]);
        
        /** @see ClassMethodHydrator::hydrate() */
        $hydrator->hydrate([
            'foo' => '11',
            'bar_baz' => '22',
            'q_u_x' => '33',
            'x_y_zy' => '44',
        ], $obj);
        
        self::assertEquals('11', $obj->getFoo());
        self::assertEquals('22', $obj->getBarBaz());
        self::assertEquals('33', $obj->getQUX());
        self::assertEquals('44', $obj->getXYZy());
    }
    
    public function testExtract()
    {
        $obj = new SampleMethodClass();
        $obj->setFoo('55');
        $obj->setBarBaz('66');
        $obj->setQUX('77');
        $obj->setXYZy('88');
        
        $factory = new HydratorFactory();
        $hydrator = $factory->create([
            'type' => 'ClassMethod',
            'options' => [
                'getterNamingStrategy' => 'CamelCase2Underscore',
            ],
        ]);
        
        /** @see ClassMethodHydrator::extract() */
        self::assertEquals([
            'foo' => '55',
            'bar_baz' => '66',
            'q_u_x' => '77',
            'x_y_zy' => '88',
        ], $hydrator->extract($obj));
    }
}
