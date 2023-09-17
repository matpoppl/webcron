<?php

namespace matpoppl\Navigation\Node;

interface TreeNodeInterface extends ContainerInterface
{
    /** @return LinkNode */
    public function getLink();
    
    /** @return ItemNode */
    public function getItem();
    
    /** @return ListNode */
    public function getList();
    
    /** @return TreeNodeInterface|NULL */
    public function getParentNode();
}
