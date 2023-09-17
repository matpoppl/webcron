<?php

namespace matpoppl\DBAL;

interface DriverInterface
{
    /**
     * 
     * @param string $sql
     * @param array $params
     * @return StatementInterface
     */
    public function prepare($sql, array $params = null);
    
    /**
     *
     * @param string $sql
     * @param array $params
     * @return StatementInterface
     */
    public function query($sql, array $params = null);
    
    /**
     *
     * @param string $sql
     * @return bool
     */
    public function exec($sql);
    
    /** @return string */
    public function getLastInsertedId();
    
    /** @return int */
    public function getAffectedCount();
    
    /**
     * 
     * @param string $str
     * @return string
     */
    public function escape($str);
    
    /** @return string[] */
    public function listTableNames();
    
    /** @return array */
    public function getLastError();
    
    public function getDriver();
}
