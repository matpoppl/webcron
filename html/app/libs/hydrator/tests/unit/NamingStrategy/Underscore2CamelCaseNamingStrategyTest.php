<?php

namespace matpoppl\Hydrator\NamingStrategy;

use PHPUnit\Framework\TestCase;

class Underscore2CamelCaseNamingStrategyTest extends TestCase
{
    public function testInvoke()
    {
        $ns = new Underscore2CamelCaseNamingStrategy();
        
        foreach ([
            'foo_bar_baz' => 'fooBarBaz',
            'foo__bar__baz' => 'fooBarBaz',
            'f_f_b_b_q_q' => 'fFBBQQ',
        ] as $str => $expected) {
            self::assertEquals($expected, $ns($str));
        }
    }
}
