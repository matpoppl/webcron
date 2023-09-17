<?php

namespace matpoppl\DBAL\SQLite;

use matpoppl\DBAL\DriverInterface;
use SQLite3;

class SQLiteDriver implements DriverInterface
{
    /** @var array */
    private $options = null;
    /** @var \SQLite3 */
    private $driver = null;
    
    public function __construct(array $options)
    {
        if (! class_exists(SQLite3::class)) {
            throw new \UnexpectedValueException('SQLite3 module required');
        }
        
        $this->options = $options;
    }
    
    /** @return \SQLite3 */
    public function getDriver()
    {
        if (null === $this->driver) {
            $this->driver = new SQLite3($this->options['dsn']/** @TODO $flags, $key */);
        }
        
        return $this->driver;
    }
    
    public function getLastError()
    {
        if (null === $this->driver) {
            throw new \UnexpectedValueException('Not connected');
        }
        
        return [$this->getDriver()->lastErrorMsg(), $this->getDriver()->lastErrorCode()];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::prepare()
     */
    public function prepare($sql, array $params = null)
    {
        $stmt = $this->getDriver()->prepare($sql);
        
        if (! $stmt) {
            throw new \UnexpectedValueException(json_encode($this->getLastError()));
        }
        
        return new SqliteStatement($this, $stmt, $sql, $params);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::query()
     */
    public function query($sql, array $params = null)
    {
        return $this->prepare($sql)->exec($params);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::exec()
     */
    public function exec($sql)
    {
        $ok = $this->getDriver()->exec($sql);
        
        if (! $ok) {
            throw new \UnexpectedValueException($this->getDriver()->lastErrorMsg(), $this->getDriver()->lastErrorCode());
        }
        
        return true;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::getLastInsertedId()
     */
    public function getLastInsertedId()
    {
        return $this->getDriver()->lastInsertRowID();
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::getAffectedCount()
     */
    public function getAffectedCount()
    {
        return $this->getDriver()->changes();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \matpoppl\DBAL\DriverInterface::escape()
     */
    public function escape($str)
    {
        return $this->getDriver()->escapeString($str);
    }
    
    public function listTableNames()
    {
        $ret = [];
        foreach ($this->query('SELECT `name` FROM `sqlite_schema` WHERE `type` = \'table\' AND `name` NOT LIKE \'sqlite_%\'') as $row) {
            $ret[] = $row['name'];
        }
        return $ret;
    }
    
    public function describeTable($tableName)
    {
        foreach ($this->query('SELECT `sql` FROM `sqlite_schema` WHERE `type` = \'table\' AND `name`=?', [$tableName]) as $row) {
            return $row['sql'];
        }
        return null;
    }
}
