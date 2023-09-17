<?php

namespace matpoppl\Form\View;

use const ENT_QUOTES;
use matpoppl\Form\Element\ElementInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractElementView implements ElementlViewInterface
{
    /** @var ElementInterface */
    protected $element;
    /** @var ContainerInterface */
    protected $container;
    /** @var ElementViewFactoryInterface */
    protected $viewElementFactory;
    
    protected $templateRow = '<div{{ATTRS}}>{{BLOCKS}}</div>';
    protected $templateMessages = '<div class="form__messages">{{MSG}}</div>';
    protected $templateBlocks = '{{VIEW}}{{MSG}}';

    public function __construct(ContainerInterface $container, ElementInterface $element, ElementViewFactoryInterface $viewElementFactory)
    {
        $this->container = $container;
        $this->element = $element;
        $this->viewElementFactory = $viewElementFactory;
    }
    
    /** @return ElementInterface */
    public function getElement()
    {
        return $this->element;
    }
    
    /** @return string */
    public function getViewType()
    {
        $name = basename(static::class);
        $type = ('View' === substr($name, -4)) ? substr($name, 0, -4) : $name;
        return strtolower($type);
    }
    
    public function offsetExists($name) : bool
    {
        return $this->element->getAttributes()->has($name);
    }

    public function offsetGet($name) : mixed
    {
        return $this->element->getAttributes()->get($name);
    }

    public function offsetSet($name, $val) : void
    {
        $this->element->getAttributes()->set($name, $val);
    }

    public function offsetUnset($name) : void
    {
        $this->element->getAttributes()->remove($name);
    }
    
    public function renderFormRow()
    {
        $msgs = $this->renderMessagesOf('error') . $this->renderMessagesOf('desc');
        
        if (!empty($msgs)) {
            $msgs = strtr($this->templateMessages, [
                '{{MSG}}' => $msgs,
            ]);
        }
        
        $parts = [
            '{{VIEW}}' => $this->renderView(),
            '{{MSG}}' => $msgs,
        ];
        
        $rowAttrs = '';
        
        return strtr($this->templateRow, [
            '{{ATTRS}}' => $rowAttrs,
            '{{BLOCKS}}' => strtr($this->templateBlocks, $parts),
        ]);
    }
    
    public function renderMessagesOf($type)
    {
        $msg = $this->element->getMessagesOf($type);
        return empty($msg) ? '' : '<ul class="form__msg form__msg--'.$type.'"><li>'.implode('</li><li>', array_map(function($str){
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }, $msg)).'</li></ul>';
    }
}
