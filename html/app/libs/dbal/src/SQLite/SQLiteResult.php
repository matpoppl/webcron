<?php

namespace matpoppl\DBAL\SQLite;

class SQLiteResult implements \Countable, \Iterator
{
    /** @var \SQLite3Result */
    private $result;
    private $fetchMode = \SQLITE3_ASSOC;
    private $count = null;
    private $iterCurrent = false;
    private $iterPointer = 0;
    
    public function __construct(\SQLite3Result $result)
    {
        $this->result = $result;
    }
    
    public function __destruct()
    {
        $this->result->finalize();
    }
    
    /** @return \SQLite3Stmt */
    public function getResult()
    {
        return $this->result;
    }
    
    public function getAffectedCount()
    {
        return $this->result->numColumns();
    }
    
    public function count(): int
    {
        if (null === $this->count) {
            $this->count = $this->result->numColumns() > 0 ? iterator_count($this) : 0;
            $this->result->reset();
        }
        
        return $this->count;
    }
    
    public function rewind(): void
    {
        if (! $this->result->reset()) {
            throw new \UnexpectedValueException('Reset failed');
        }
        
        $this->iterPointer = 0;
        $this->iterCurrent = $this->result->fetchArray($this->fetchMode);
    }
    
    public function valid(): bool
    {
        return false !== $this->iterCurrent;
    }
    
    public function key(): mixed
    {
        return $this->iterPointer;
    }
    
    public function current(): mixed
    {
        return $this->iterCurrent;
    }
    
    public function next(): void
    {
        $this->iterPointer++;
        $this->iterCurrent = $this->result->fetchArray($this->fetchMode);
    }
    
    public function fetchAll()
    {
        return iterator_to_array($this);
    }
}
