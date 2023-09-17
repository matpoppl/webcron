<?php

namespace matpoppl\Form\View;

use PHPUnit\Framework\TestCase;
use matpoppl\Form\FormBuilder;
use matpoppl\ServiceManager\ServiceManager;

class AbstractElementViewTest extends TestCase
{
    public function testAttributess()
    {
        $container = new ServiceManager([]);
        
        $factory = new FormBuilder($container);
        
        $form = $factory->createForm([
            'attributes' => [
                'foo' => 'bar',
            ],
        ]);
        
        $view = $form->getView();
        
        self::assertTrue(isset($view['foo']));
        self::assertEquals('bar', $view['foo']);
        
        $view['foo'] = 'baz';
        self::assertEquals('baz', $view['foo']);
        
        unset($view['foo']);
        self::assertFalse(isset($view['foo']));
    }
}
