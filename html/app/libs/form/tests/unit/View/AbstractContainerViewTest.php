<?php

namespace matpoppl\Form\View;

use PHPUnit\Framework\TestCase;
use matpoppl\Form\FormBuilder;
use matpoppl\ServiceManager\ServiceManager;

class AbstractContainerViewTest extends TestCase
{
    public function testRenameElements()
    {
        $container = new ServiceManager([]);
        
        $factory = new FormBuilder($container);
        
        $form = $factory->createForm([
            'attributes' => [
                'id' => 'base-id',
                'name' => 'base-name',
            ],
            'elements' => [
                'foo' => new \matpoppl\Form\Element\FieldsetElement($container, [
                    'elements' => [
                        'bar' => new \matpoppl\Form\Element\FieldsetElement($container, [
                            'elements' => [
                                'baz' => [
                                    new \matpoppl\Form\Element\InputElement($container, [])
                                ],
                            ],
                        ]),
                    ],
                ])
            ],
        ]);
        
        $view = $form->getView();
        
        $view->renameElements();
        
        $attrs = $view->get('foo')->getElement()->getAttributes();
        self::assertEquals('base-id-foo', $attrs->get('id'));
        self::assertEquals('base-name[foo]', $attrs->get('name'));
        
        $attrs = $view->get('foo')->get('bar')->getElement()->getAttributes();
        self::assertEquals('base-id-foo-bar', $attrs->get('id'));
        self::assertEquals('base-name[foo][bar]', $attrs->get('name'));
        
        $attrs = $view->get('foo')->get('bar')->get('baz')->getElement()->getAttributes();
        self::assertEquals('base-id-foo-bar-baz', $attrs->get('id'));
        self::assertEquals('base-name[foo][bar][baz]', $attrs->get('name'));
    }
}
