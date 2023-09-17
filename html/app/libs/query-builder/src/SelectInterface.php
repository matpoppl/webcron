<?php

namespace matpoppl\QueryBuilder;

interface SelectInterface extends ConditionQueryInterface
{
    /**
     * 
     * @param array|string $tableName
     * @param array|string $columns
     * @return SelectInterface
     */
    public function from($tableName, $columns = null) : SelectInterface;
    
    /**
     *
     * @param array|string $tableName
     * @param string $condition
     * @param array|string $columns
     * @return SelectInterface
     */
    public function join($tableName, $condition, $columns = null) : SelectInterface;
    
    public function limit($limit, $offset = null) : SelectInterface;
    
    public function order($by) : SelectInterface;
}
