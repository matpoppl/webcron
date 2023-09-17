<?php

namespace matpoppl\Navigation\Utils;

use matpoppl\Navigation\Node\TreeNode;

class TreeIterator implements \RecursiveIterator, \Countable
{
    private $node;
    
    private $iterKeys;
    private $iterCount;
    private $iterPointer;
    
    public function __construct(TreeNode $node)
    {
        $this->node = $node;
    }
    
    public function rewind() : void
    {
        $this->iterPointer = 0;
        $this->iterKeys = $this->node->getKeys();
        $this->iterCount = count($this->iterKeys);
    }
    
    public function valid() : bool
    {
        return $this->iterPointer < $this->iterCount;
    }
    
    public function key() : mixed
    {
        return $this->iterKeys[$this->iterPointer];
    }
    
    public function current() : mixed
    {
        return $this->node->get($this->key());
    }

    public function next() : void
    {
        $this->iterPointer++;
    }
    
    public function hasChildren(): bool
    {
        return count($this->current()) > 0;
    }
    
    public function getChildren(): ?\RecursiveIterator
    {
        return new static($this->current());
    }
    
    public function count(): int
    {
        return count($this->node);
    }
}
