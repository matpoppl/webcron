<?php

namespace matpoppl\EntityManager;

use PHPUnit\Framework\TestCase;
use matpoppl\Hydrator\HydratorInterface;
use matpoppl\Hydrator\NamingStrategy\NamingStrategyInterface;

class EntitySpecsTest extends TestCase
{
    public function testHydratorExtract()
    {
        $specs = new EntitySpecs([
            'hydrator' => [
                'type' => 'ClassMethod',
                'options' => [
                    'getterNamingStrategy' => 'CamelCase2Underscore',
                ],
            ],
            'columns' => [
                'foo' => 'foo',
                'bar_baz' => 'bar_baz',
            ],
        ]);
        
        $expected = [
            'foo' => 222,
            'bar_baz' => 333,
        ];
        
        $entity = new SampleEntity();
        $entity->exchangeArray($expected);
        
        self::assertEquals(222, $entity->getFoo());
        self::assertEquals(333, $entity->getBarBaz());
        
        self::assertInstanceOf(HydratorInterface::class, $specs->getHydrator());
        self::assertInstanceOf(NamingStrategyInterface::class, $specs->getHydrator()->getGetterNamingStrategy());
        
        self::assertEquals($expected, $specs->extract($entity));
    }
    
}
