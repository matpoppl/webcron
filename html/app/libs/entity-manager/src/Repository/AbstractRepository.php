<?php

namespace matpoppl\EntityManager\Repository;

use matpoppl\DBAL\DBALInterface;
use matpoppl\EntityManager\EntityInterface;
use matpoppl\EntityManager\EntitySpecs;
use matpoppl\QueryBuilder\QueryBuilderInterface;
use matpoppl\QueryBuilder\SelectInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    /** @var EntitySpecs */
    protected $specs;
    /** @var DbalInterface */
    protected $dbal;
    /** @var QueryBuilderInterface */
    protected $qb;
    /** @var string */
    protected $fetchMode = self::FETCH_ENTITY;
    
    public function __construct(EntitySpecs $specs, DBALInterface $dbal, QueryBuilderInterface $qb)
    {
        $this->specs = $specs;
        $this->dbal = $dbal;
        $this->qb = $qb;
    }
    
    /** @return EntitySpecs */
    public function getEntitySpecs()
    {
        return $this->specs;
    }
    
    public function setFetchMode($fetchMode)
    {
        switch ($fetchMode) {
            case self::FETCH_ARRAY:
            case self::FETCH_DBAL_STMT:
            case self::FETCH_ENTITY:
                $this->fetchMode = $fetchMode;
                return $this;
        }
        
        throw new \DomainException('Unsupported fetch mode `'.$fetchMode.'`');
    }
    
    public function save(EntityInterface $entity)
    {
        if ($entity->isNewEntity()) {
            return $this->insert($entity);
        }
        
        return $this->update($entity);
    }
    
    public function insert(EntityInterface $entity)
    {
        $data = $this->specs->extract($entity);
        
        $query = $this->qb->insert()->into("{{$this->specs->getTableName()}}")->values($data);
        
        $query = $this->qb->build($query);
        
        $this->dbal->prepare($query->getSql())->exec($query->getParams());
        
        $affectedCount = $this->dbal->getAffectedCount();
        
        if ($affectedCount < 1) {
            throw new \UnexpectedValueException('Entity save error');
        }
        
        if ($idProperty = $this->specs->getSequenceColumn()) {
            $this->specs->hydrate([
                $idProperty => $this->dbal->getLastInsertedId()
            ], $entity);
        }
        
        return $affectedCount;
    }
    
    public function update(EntityInterface $entity)
    {
        $where = $this->specs->extractPKs($entity);
        $set = array_diff_key($this->specs->extract($entity), $where);
        
        $query = $this->qb->update()->table("{{$this->specs->getTableName()}}")->set($set)->where($where);
        
        $query = $this->qb->build($query);
        
        $this->dbal->prepare($query->getSql())->exec($query->getParams());
        
        return $this->dbal->getAffectedCount();
    }
    
    public function remove(EntityInterface $entity)
    {
        $where = $this->specs->extractPKs($entity);
        
        $query = $this->qb->delete()->table($this->specs->getTableName())->where($where);
        
        $query = $this->qb->build($query);
        
        $this->dbal->prepare($query->getSql())->exec($query->getParams());
        
        return $this->dbal->getAffectedCount();
    }
    
    public function fetchRow($where, $order = null, $offset = null)
    {
        if ($where instanceof SelectInterface) {
            $query = $where;
        } else {
            $query = $this->qb->select()->from("{{$this->specs->getTableName()}}");
            $this->qb->populateWhere($query, $where);
        }
        
        if ($order) {
            $query->order($order);
        }
        
        $query->limit(1, $offset);
        
        $query = $this->qb->build($query);
        
        $stmt = $this->dbal->prepare($query->getSql(), $query->getParams());
        
        switch ($this->fetchMode) {
            case self::FETCH_DBAL_STMT:
                return $stmt;
        }
        
        foreach ($stmt as $row) {
            switch ($this->fetchMode) {
                case self::FETCH_ENTITY:
                    return $this->specs->createEntity($row, false);
                case self::FETCH_ARRAY:
                    return $row;
            }
        }
        
        return null;
    }
    
    public function fetchRows($where, $order = null, $limit = null, $offset = null)
    {
        $query = $this->qb->select()->from("{{$this->specs->getTableName()}}");
        
        $query = $this->qb->build($query);
        
        $ret = [];
        $stmt = $this->dbal->prepare($query->getSql(), $query->getParams());
        
        switch ($this->fetchMode) {
            case self::FETCH_DBAL_STMT:
                return $stmt;
            case self::FETCH_ENTITY:
                foreach ($stmt as $row) {
                    $ret[] = $this->specs->createEntity($row, false);
                }
                break;
            case self::FETCH_ARRAY:
                foreach ($stmt as $row) {
                    $ret[] = $row;
                }
                break;
        }
        
        return $ret;
    }
    
    public function find($id)
    {
        $query = $this->qb->select()->from("{{$this->specs->getTableName()}}")->where('id=:id', (int) $id);
        
        $query = $this->qb->build($query);
        
        foreach ($this->dbal->prepare($query->getSql(), $query->getParams()) as $row) {
            return $this->specs->createEntity($row, false);
        }
        
        return null;
    }
}
