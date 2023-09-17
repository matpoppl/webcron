<?php

namespace matpoppl\Navigation;

use matpoppl\Navigation\Node\ContainerInterface;
use matpoppl\Navigation\Node\TreeNode;

class Navigation implements ContainerInterface
{
    /** @var ContainerInterface */
    private $root;
    
    /** @var ItemFactory */
    private $itemFactory;
    
    public function __construct(ItemFactory $itemFactory, array $options = null)
    {
        $this->itemFactory = $itemFactory;
        $this->root = new TreeNode($itemFactory, $options);
    }
    
    /** @return ContainerInterface */
    public function getRoot()
    {
        return $this->root;
    }
    
    public function add($item, $name = null)
    {
        $this->root->add($item, $name);
        return $this;
    }
    
    public function get($name)
    {
        return $this->root->get($name);
    }
    
    public function getIterator() : \Traversable
    {
        return $this->root->getIterator();
    }
    
    public function getKeys()
    {
        return $this->root->getKeys();
    }
    
    public function findByUri($uri)
    {
        return $this->root->findByUri($uri);
    }
}
