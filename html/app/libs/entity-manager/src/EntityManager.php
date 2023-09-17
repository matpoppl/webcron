<?php

namespace matpoppl\EntityManager;

use Psr\Container\ContainerInterface;
use matpoppl\EntityManager\Repository\RepositoryInterface;

class EntityManager implements EntityManagerInterface
{
    /** @var array */
    private $options;
    /** @var EntitySpecs[] */
    private $specs = [];
    
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container, array $options)
    {
        $this->container = $container;
        $this->options = $options;
    }
    
    /**
     * 
     * @param string|object $entity
     * @return \matpoppl\EntityManager\EntitySpecs
     */
    public function getEntitySpecs($entity)
    {
        $key = is_object($entity) ? get_class($entity) : $entity;
        
        if (! array_key_exists($key, $this->specs)) {
            $this->specs[$key] = new EntitySpecs($this->options[$key] ?? []);
        }
        
        return $this->specs[$key];
    }
    
    public function save(EntityInterface $entity)
    {
        return $this->getRepository($entity)->save($entity);
    }
    
    public function remove(EntityInterface $entity)
    {
        return $this->getRepository($entity)->remove($entity);
    }
    
    /**
     * 
     * @param string|EntityInterface $entity
     * @return RepositoryInterface
     */
    public function getRepository($entity)
    {
        return $this->getEntitySpecs($entity)->getRepository( $this->container );
    }
    
    public function fetchRows($entity, $fetchMode, $where, $order = null, $limit = null, $offset = null)
    {
        return $this->getRepository($entity)->setFetchMode($fetchMode)->fetchRows($where, $order, $limit, $offset);
    }
    
    public function find($entity, $fetchMode, $id)
    {
        return $this->getRepository($entity)->setFetchMode($fetchMode)->find($id);
    }
}
