<?php

namespace matpoppl\QueryBuilder;

interface InsertInterface extends QueryInterface
{
    /**
     * 
     * @param string $tableName
     * @return InsertInterface
     */
    public function into(string $tableName) : InsertInterface;
    
    /**
     *
     * @param string $tableName
     * @return InsertInterface
     */
    public function values(array $values) : InsertInterface;
    
    /**
     * 
     * @return string
     */
    public function getTableName() : string;
    
    /**
     * 
     * @return array[]
     */
    public function getValues() : array;
}
