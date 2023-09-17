<?php

namespace matpoppl\Hydrator;

use PHPUnit\Framework\TestCase;

class ObjectPropertyHydratorTest extends TestCase
{
    public function testHydrate()
    {
        $obj = new SamplePropertyClass();
        
        self::assertEquals(null, $obj->foo);
        self::assertEquals(null, $obj->barBaz);
        self::assertEquals(null, $obj->qUX);
        self::assertEquals(null, $obj->vXYZy);
        
        $factory = new HydratorFactory();
        $hydrator = $factory->create([
            'type' => 'ObjectProperty',
            'options' => [
                'setterNamingStrategy' => 'Underscore2CamelCase',
            ],
        ]);
        
        /** @see ObjectPropertyHydrator::hydrate() */
        $hydrator->hydrate([
            'foo' => '11',
            'bar_baz' => '22',
            'q_u_x' => '33',
            'v_x_y_zy' => '44',
        ], $obj);
        
        self::assertEquals('11', $obj->foo);
        self::assertEquals('22', $obj->barBaz);
        self::assertEquals('33', $obj->qUX);
        self::assertEquals('44', $obj->vXYZy);
    }
    
    public function testExtract()
    {
        $obj = new SamplePropertyClass();
        $obj->foo = '55';
        $obj->barBaz = '66';
        $obj->QUX = '77';
        $obj->vXYZy = '88';
        
        $factory = new HydratorFactory();
        $hydrator = $factory->create([
            'type' => 'ObjectProperty',
            'options' => [
                'getterNamingStrategy' => 'CamelCase2Underscore',
            ],
        ]);
        
        /** @see ObjectPropertyHydrator::extract() */
        self::assertEquals([
            'foo' => '55',
            'bar_baz' => '66',
            'q_u_x' => '77',
            'v_x_y_zy' => '88',
        ], $hydrator->extract($obj));
    }
}
