<?php

namespace matpoppl\QueryBuilder;

class SelectQuery extends AbstractQuery implements SelectInterface
{
    private $tableName;
    private $columns = null;
    private $values = [];
    private $conditions = [];
    private $joins = [];
    private $limit = [null, null];
    
    public function from($tableName, $columns = null) : SelectInterface
    {   
        $alias = null;
        if (is_array($tableName)) {
            $alias = key($tableName);
            $tableName = current($tableName);
            $this->tableName = "`{$tableName}` AS {$alias}";
        } else {
            $this->tableName = $tableName;
        }
        
        if (null !== $columns) {
            $this->addColumns($columns, $alias);
        }
        
        return $this;
    }
    
    public function addColumns($columns, $alias = null) : SelectInterface
    {
        if (null === $this->columns) {
            $this->columns = [];
        }
        
        if (is_array($columns)) {
            if (null === $alias) {
                $this->columns = array_merge($this->columns, $columns);
            } else {
                foreach ($columns as $key => $column) {
                    $this->columns[] = "{$alias}.`{$column}`" . (is_string($key) ? " AS {$key}" : '');
                }
            }
        } else {
            $this->columns[] = $columns;
        }
        
        return $this;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    public function join($tableName, $condition, $columns = null) : SelectInterface
    {
        $alias = null;
        if (is_array($tableName)) {
            $alias = key($tableName);
            $tableName = current($tableName);
            $tableName = "`{$tableName}` AS {$alias}";
        }
        
        $this->joins[] = [
            'joined_table' => 'JOIN',
            'table_factor' => $tableName,
            'join_specification' => "ON ({$condition})",
        ];
        
        if (null !== $columns) {
            $this->addColumns($columns, $alias);
        }
        
        return $this;
    }
    
    public function getJoins() : array
    {
        return $this->joins;
    }
    
    public function where($condition, $value = null) : SelectInterface
    {
        if (is_array($condition)) {
            foreach ($condition as $cond1 => $val1) {
                $this->where($cond1, $val1);
            }
            return $this;
        }
        
        $this->conditions[] = $condition;
        
        $matched = null;
        if (preg_match_all('#(\?|\:\w+)#', $condition, $matched) > 0) {
            foreach ($matched[1] as $name) {
                if ('?' === $name) {
                    $this->values[] = $value;
                } else {
                    $this->values[$name] = $value;
                }
            }
        }
        
        return $this;
    }
    
    public function limit($limit, $offset = null) : SelectInterface
    {
        $this->limit = [$offset, $limit];
        return $this;
    }
    
    public function order($by) : SelectInterface
    {
        return $this;
    }
    
    public function getLimit() : array
    {
        return $this->limit;
    }
    
    public function getTableName() : string
    {
        return $this->tableName;
    }
    
    public function getValues() : array
    {
        return $this->values;
    }
    
    public function getConditions() : array
    {
        return $this->conditions;
    }
}
