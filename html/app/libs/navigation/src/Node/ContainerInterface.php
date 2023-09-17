<?php

namespace matpoppl\Navigation\Node;

interface ContainerInterface extends \IteratorAggregate
{
    /**
     * @param array|ItemNode $item
     * @param string|NULL $name
     * @return static
     */
    public function add($item, $name = null);
    
    /**
     * @throws \DomainException
     * @param string|NULL $name
     * @return static
     */
    public function get($name);
    
    /** @return string[] */
    public function getKeys();
    
    /** @return \matpoppl\Navigation\Utils\TreeIterator */
    public function getIterator() : \Traversable;
    
    /**
     * @param string $uri
     * @return ContainerInterface|NULL
     */
    public function findByUri($uri);
}
