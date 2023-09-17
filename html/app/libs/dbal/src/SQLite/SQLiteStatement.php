<?php

namespace matpoppl\DBAL\SQLite;

use matpoppl\DBAL\StatementInterface;

class SQLiteStatement implements StatementInterface, \IteratorAggregate
{
    /** @var SQLiteDriver */
    private $driver;
    /** @var \SQLite3Stmt */
    private $stmt;
    /** @var string */
    private $sql;
    /** @var array */
    private $params;
    /** @var SQLiteResult|NULL */
    private $result = null;
    
    public function __construct(SQLiteDriver $driver, \SQLite3Stmt $stmt, $sql, array $params = null)
    {
        $this->driver = $driver;
        $this->stmt = $stmt;
        $this->sql = $sql;
        $this->params = $params;
    }
    
    /** @return \SQLite3Stmt */
    public function getStatement()
    {
        return $this->stmt;
    }
    
    /** @return SQLiteResult|NULL */
    public function getResult()
    {
        return $this->result;
    }
    
    public function exec(array $params = null)
    {
        if ($this->result) {
            $this->result = null;
            $this->stmt->reset();
        }
        
        if (null === $params) {
            $params = $this->params;
        }
        
        if (is_array($params)) {
            foreach (array_keys($params) as $name) {
                // 1+ because sqlite begins with 1 not 0
                $this->stmt->bindValue(is_int($name) ? 1 + $name : $name, $params[$name]);
            }
        }
        
        try {
            $result = $this->stmt->execute();
        } catch (\Exception $ex) {
            $result = null;
        }
        
        if (! $result) {
            throw new \UnexpectedValueException(json_encode($this->driver->getLastError()) . "\n{$this->sql}");
        }
        
        $this->result = new SQLiteResult($result);
        
        return $this;
    }
    
    public function count(): int
    {
        if (null === $this->result) {
            $this->exec();
        }
        
        return $this->result->count();
    }
    
    public function getIterator() : \Traversable
    {
        if (null === $this->result) {
            $this->exec();
        }
        
        return $this->result;
    }
}
