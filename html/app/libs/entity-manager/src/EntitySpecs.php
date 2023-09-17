<?php

namespace matpoppl\EntityManager;

use matpoppl\Hydrator\HydratorInterface;
use Psr\Container\ContainerInterface;
use matpoppl\Hydrator\HydratorFactory;

class EntitySpecs
{
    /** @var array */
    private $options;
    
    public function __construct(array $options)
    {
        $this->options = $options;
    }
    
    public function getPKs()
    {
        return $this->options['pk'] ?? [];
    }
    
    public function extractPKs($entity)
    {
        $data = $this->extract($entity);
        $pks = $this->getPKs();
        return array_intersect_key($data, array_combine($pks, $pks));
    }
    
    public function getTableName()
    {
        return $this->options['tableName'] ?? null;
    }
    
    public function getSequenceColumn()
    {
        return $this->options['seqCol'] ?? null;
    }
    
    public function getRepository(ContainerInterface $container)
    {
        return $container->create( $this->options['repository'] ?? null, $this );
    }
    
    public function createEntity(array $data = null, $isNewRecord = null)
    {
        $className = $this->options['className'];
        $entity = new $className();
        
        if ($entity instanceof EntityInterface) {
            $entity->isNewEntity($isNewRecord);
        }
        
        if (null !== $data) {
            $this->hydrate($data, $entity);
        }
        return $entity;
    }
    
    /**
     * @throws \UnexpectedValueException
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        $factory = new HydratorFactory();
        return $factory->create($this->options['hydrator'] ?? null);
    }
    
    public function extract($entity)
    {
        $columns = $this->getHydrator()->extract($entity);
        
        if (! isset($this->options['columns'])) {
            return $columns;
        }
        
        $ret = [];
        foreach ($this->options['columns'] as $colName => $propName) {
            $ret[$colName] = $columns[$propName] ?? null;
        }
        
        return $ret;
    }
    
    public function hydrate(array $data, $obj)
    {
        if (isset($this->options['columns'])) {
            foreach ($this->options['columns'] as $colName => $propName) {
                if (! array_key_exists($propName, $data) && array_key_exists($colName, $data)) {
                    $data[$propName] = $data[$colName];
                }
            }
        }
        
        $this->getHydrator()->hydrate($data, $obj);
        
        return $this;
    }
}
