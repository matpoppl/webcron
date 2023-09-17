<?php

namespace matpoppl\QueryBuilder;

class DeleteQuery extends AbstractQuery implements DeleteInterface
{
    private $tableName;
    private $conditions = [];
    
    public function table($tableName) : DeleteInterface
    {
        $this->tableName = $tableName;
        return $this;
    }
    
    public function where($condition, $value = null) : DeleteInterface
    {
        if (is_array($condition)) {
            foreach ($condition as $cond1 => $val1) {
                $this->where($cond1, $val1);
            }
            return $this;
        }
        
        $this->conditions[$condition] = $value;
        
        return $this;
    }
    
    public function getTableName() : string
    {
        return $this->tableName;
    }
    
    public function getConditions() : array
    {
        return $this->conditions;
    }
}
