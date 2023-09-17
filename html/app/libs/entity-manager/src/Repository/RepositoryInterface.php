<?php

namespace matpoppl\EntityManager\Repository;

use matpoppl\EntityManager\EntityInterface;
use matpoppl\QueryBuilder\SelectInterface;
use matpoppl\EntityManager\EntitySpecs;

interface RepositoryInterface
{
    const FETCH_ARRAY = 'array';
    const FETCH_ENTITY = 'entity';
    const FETCH_DBAL_STMT = 'dbal_stmt';
    
    /** @return EntitySpecs */
    public function getEntitySpecs();
    
    public function save(EntityInterface $entity);
    public function remove(EntityInterface $entity);
    
    /**
     * 
     * @param string $fetchMode
     * @return static
     */
    public function setFetchMode($fetchMode);
    
    /**
     * 
     * @param SelectInterface|array $where
     * @param array|NULL $order
     * @param int|NULL $offset
     * @return EntityInterface|NULL
     */
    public function fetchRow($where, $order = null, $offset = null);
    
    /**
     * 
     * @param SelectInterface|array $where
     * @param array|NULL $order
     * @param int|NULL $limit
     * @param int|NULL $offset
     * @return EntityInterface[]
     */
    public function fetchRows($where, $order = null, $limit = null, $offset = null);
}
