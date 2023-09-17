<?php

namespace matpoppl\Navigation;

use matpoppl\Navigation\Node\LinkNode;
use matpoppl\Navigation\Node\ItemNode;
use matpoppl\Navigation\Node\ListNode;
use matpoppl\Navigation\Node\TreeNode;
use matpoppl\SmallMVC\Router\Router;

class ItemFactory
{
    /** @var Router */
    private $router;
    
    public function __construct(Router $router = null)
    {
        $this->router = $router;
    }
    
    public function create(array $options)
    {
        $ret = new TreeNode($this);
        
        if (isset($options['items'])) {
            $ret->setItems($options['items']);
            unset($options['items']);
        }
        
        if (isset($options['item'])) {
            $ret->setItem($options['item']);
            unset($options['item']);
        }
        
        if (isset($options['list'])) {
            $ret->setList($options['list']);
            unset($options['list']);
        }
        
        if (isset($options['link'])) {
            $options = $options['link'];
            unset($options['link']);
        }
        
        $ret->setLink($this->createLink($options));
        
        return $ret;
    }
    
    public function createLink(array $options)
    {
        $ret = new LinkNode($options);
        
        if (isset($options['uri'])) {
            $ret->setUri( $options['uri'] );
        } else if (isset($options['path'])) {
            $ret->setUri( $this->router->get($options['path'])->buildPath($options['pathParams'] ?? null) );
        }
        
        if (isset($options['label'])) {
            $ret->setLabel( $options['label'] );
        }
        
        return $ret;
    }
    
    public function createItem(array $options)
    {
        $ret = new ItemNode($options);
        
        return $ret;
    }
    
    public function createList(array $options)
    {
        $ret = new ListNode($options);
        
        return $ret;
    }
}
