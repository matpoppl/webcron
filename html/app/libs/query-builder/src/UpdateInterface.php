<?php

namespace matpoppl\QueryBuilder;

interface UpdateInterface extends ConditionQueryInterface
{
    public function table($tableName) : UpdateInterface;
    
    public function set($condition, $value = null) : UpdateInterface;
    
    public function getTableName() : string;
    
    public function getAssignments() : array;
}
