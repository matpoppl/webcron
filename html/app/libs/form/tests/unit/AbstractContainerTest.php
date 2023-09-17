<?php
/**
 * !!File header!!
 */
namespace matpoppl\Form;

use PHPUnit\Framework\TestCase;
use matpoppl\ServiceManager\ServiceManager;

class AbstractContainerTest extends TestCase
{
    public function testGettersSetters()
    {
        $container = new ServiceManager([]);
        
        $factory = new FormBuilder($container);
        
        $form = $factory->createForm([
            'elements' => [
                'foo' => new \matpoppl\Form\Element\InputElement($container, [
                    'type' => 'text',
                ])
            ],
        ]);
        
        self::assertFalse($form->has('bar'));
        
        self::assertTrue($form->has('foo'));
        self::assertTrue($form->get('foo') instanceof Element\InputElement);
        self::assertEquals(['foo'], $form->getFieldList());
        $form->remove('foo');
        self::assertFalse($form->has('foo'));
    }
}
