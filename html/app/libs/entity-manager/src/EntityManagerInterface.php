<?php

namespace matpoppl\EntityManager;

use matpoppl\EntityManager\Repository\RepositoryInterface;

interface EntityManagerInterface
{
    /**
     * 
     * @param string|object $entity
     * @return \matpoppl\EntityManager\EntitySpecs
     */
    public function getEntitySpecs($entity);
    
    /**
     * 
     * @param EntityInterface $entity
     * @return boolean
     */
    public function save(EntityInterface $entity);
    
    /**
     *
     * @param EntityInterface $entity
     * @return boolean
     */
    public function remove(EntityInterface $entity);
    
    /**
     * 
     * @param string|EntityInterface $entity
     * @return RepositoryInterface
     */
    public function getRepository($entity);
    
    /**
     * 
     * @param string $entity
     * @param string $fetchMode
     * @param int $id
     */
    public function find($entity, $fetchMode, $id);
    
    /**
     * 
     * @param string $entity
     * @param string $fetchMode
     * @param array $where
     * @param string|array|null $order
     * @param int|null $limit
     * @param int|null $offset
     */
    public function fetchRows($entity, $fetchMode, $where, $order = null, $limit = null, $offset = null);
}
