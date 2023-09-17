<?php

namespace matpoppl\QueryBuilder;

class InsertQuery extends AbstractQuery implements InsertInterface
{
    private $tableName;
    private $values = [];
    
    public function into(string $tableName) : InsertInterface
    {
        $this->tableName = $tableName;
        return $this;
    }
    
    public function values(array $values) : InsertInterface
    {
        $this->values[] = $values;
        return $this;
    }
    
    public function getTableName() : string
    {
        return $this->tableName;
    }
    
    public function getValues() : array
    {
        return $this->values;
    }
}
