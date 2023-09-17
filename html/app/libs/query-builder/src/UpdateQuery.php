<?php

namespace matpoppl\QueryBuilder;

class UpdateQuery extends AbstractQuery implements UpdateInterface
{
    private $tableName;
    private $conditions = [];
    private $assignments = [];
    
    public function table($tableName) : UpdateInterface
    {
        $this->tableName = $tableName;
        return $this;
    }
    
    public function set($condition, $value = null) : UpdateInterface
    {
        if (is_array($condition)) {
            foreach ($condition as $cond1 => $val1) {
                $this->set($cond1, $val1);
            }
            return $this;
        }
        
        $this->assignments[$condition] = $value;
        
        return $this;
    }
    
    public function where($condition, $value = null) : UpdateInterface
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
    
    public function getAssignments() : array
    {
        return $this->assignments;
    }
}
