<?php

namespace matpoppl\QueryBuilder;

interface ConditionQueryInterface extends QueryInterface
{
    public function where($condition, $value = null) : ConditionQueryInterface;
    
    public function getConditions() : array;
}
