<?php

namespace matpoppl\QueryBuilder;

interface DeleteInterface extends ConditionQueryInterface
{
    public function table($tableName) : DeleteInterface;
    
    public function getTableName() : string;
    
    public function getConditions() : array;
}
