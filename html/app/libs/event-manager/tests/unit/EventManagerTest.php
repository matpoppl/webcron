<?php

namespace matpoppl\EventManager;

use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{
    public function testTrigger()
    {
        $i = 0;
        
        $em = new EventManager();
        
        $em->attach('test', function($evt) use (&$i) {
            self::assertTrue($evt instanceof EventInterface);
            $i++;
        });
        $em->attach('other', function($evt) { });
        
        $em->trigger('non');
        $em->trigger('other');
        $em->trigger('test');
        self::assertTrue(1 === $i);
        
        $em->trigger('non');
        $em->trigger('other');
        $em->trigger('test');
        self::assertTrue(2 === $i);
        
        $em->trigger('non');
        $em->trigger('other');
        $em->trigger('test');
        self::assertTrue(3 === $i);
    }
}
