<?php

namespace matpoppl\Navigation\Node;

use matpoppl\Navigation\Utils\ArrayObject;
use matpoppl\Navigation\Utils\TreeIterator;
use matpoppl\Navigation\ItemFactory;

class TreeNode extends AbstractNode implements TreeNodeInterface, \Countable
{
    /** @var ItemFactory */
    private $itemFactory;
    /** @var ItemNode */
    private $item = null;
    /** @var ListNode */
    private $list = null;
    /** @var LinkNode */
    private $link = null;
    /** @var TreeNode|NULL */
    private $parentNode = null;
    /** @var ArrayObject */
    private $items = [];
    
    public function __construct(ItemFactory $itemFactory, array $config = null)
    {
        $this->itemFactory = $itemFactory;
        $this->items = new ArrayObject();
        
        if (isset($config['item'])) {
            $this->setItem($config['item']);
            unset($config['item']);
        }
        
        if (isset($config['list'])) {
            $this->setList($config['list']);
            unset($config['list']);
        }
        
        if (isset($config['link'])) {
            $this->setLink($config['link']);
            unset($config['link']);
        }
        
        if (isset($config['items'])) {
            $this->setItems($config['items']);
            unset($config['items']);
        }
        
        parent::__construct($config);
    }
    
    public function getKeys()
    {
        return $this->items->getKeys();
    }
    
    public function get($key)
    {
        if (! $this->items->has($key)) {
            throw new \DomainException('Node dont exists');
        }
        
        $child = $this->items->get($key);
        
        if (is_array($child)) {
            $child = $this->itemFactory->create($child);
            $this->items->set($key, $child);
            $child->setParentNode($this);
        }
        
        if (! ($child instanceof self)) {
            throw new \UnexpectedValueException('Unsupported item type');
        }
        
        return $child;
    }
    
    public function setItems(array $items)
    {
        $this->items = new ArrayObject();
        foreach ($items as $key => $item) {
            $this->add($item, is_string($key) ? $key : null);
        }
        return $this;
    }
    
    public function getParentNode()
    {
        return $this->parentNode;
    }
    
    public function setParentNode(TreeNodeInterface $parentNode)
    {
        $this->parentNode = $parentNode;
        return $this;
    }
    
    public function add($item, $name = null)
    {
        if (null === $name) {
            $i = count($this->items);
            $key = 'item';
            while ($this->items->has($key.$i)) {
                $i++;
            }
            $name = $key.$i;
        }
        
        $this->items->set($name, $item);
        
        return $this;
    }
    
    public function getLink()
    {
        if (is_array($this->link)) {
            $this->link = $this->itemFactory->createLink($this->link);
        } else if (null === $this->link) {
            $this->link = $this->itemFactory->createLink([]);
        }
        
        return $this->link;
    }
    
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }
    
    public function getItem()
    {
        if (is_array($this->item)) {
            $this->item = $this->itemFactory->createItem($this->item);
        } else if (null === $this->item) {
            $this->item = $this->itemFactory->createItem([]);
        }
        
        return $this->item;
    }
    
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }
    
    public function getList()
    {
        if (is_array($this->list)) {
            $this->list = $this->itemFactory->createList($this->list);
        }
        
        return $this->list;
    }
    
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }
    
    public function count(): int
    {
        return count($this->items);
    }
    
    public function getIterator() : \Traversable
    {
        return new TreeIterator($this);
    }
    
    /**
     * @param string $uri
     * @return ContainerInterface|NULL
     */
    public function findByUri($uri)
    {
        $flags = \RecursiveIteratorIterator::SELF_FIRST;
        $iter = new \RecursiveIteratorIterator($this->getIterator(), $flags);
        
        foreach ($iter as $node) {
            /** @var TreeNodeInterface $node */
            if ($uri === $node->getLink()->getUri()) {
                return $node;
            }
        }
        
        return null;
    }
    
    public function __clone()
    {
        if ($this->item) {
            $this->item = clone $this->item;
        }
        if ($this->link) {
            $this->link = clone $this->link;
        }
        if ($this->list) {
            $this->list = clone $this->list;
        }
    }
}
