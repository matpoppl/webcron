<?php

namespace matpoppl\Hydrator\NamingStrategy;

use PHPUnit\Framework\TestCase;

class CamelCase2UnderscoreNamingStrategyTest extends TestCase
{
    public function testInvoke()
    {
        $ns = new CamelCase2UnderscoreNamingStrategy();
        
        foreach ([
            'fooBarBaz' => 'foo_bar_baz',
            'FooBarBaz' => 'foo_bar_baz',
            'foo_Bar_Baz' => 'foo__bar__baz',
            'FFBBQQ' => 'f_f_b_b_q_q',
        ] as $str => $expected) {
            self::assertEquals($expected, $ns($str));
        }
    }
}
