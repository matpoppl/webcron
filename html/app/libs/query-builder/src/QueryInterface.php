<?php

namespace matpoppl\QueryBuilder;

interface QueryInterface
{
    
    /**
     *
     * @return string
     */
    public function getSql();

    /**
     * 
     * @param string $sql
     * @return QueryInterface
     */
    public function setSql($sql);
    
    /**
     *
     * @return NULL|array
     */
    public function getParams();
    
    /**
     *
     * @param array $params
     * @return QueryInterface
     */
    public function setParams(array $params);
}
