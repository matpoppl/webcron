<?php

namespace matpoppl\DBAL;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class DBALManager implements DBALInterface, LoggerAwareInterface
{
    /** @var array */
    private $options;
    /** @var array */
    private $drivers = [];
    /** @var LoggerInterface|NULL */
    private $logger;
    
    public function __construct(array $options)
    {
        $this->options = $options;
    }
    
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
    
    public function getOption($name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }
    
    /** @return DriverInterface */
    public function getDriver()
    {
        return $this->getDriverBy($this->options['default']);
    }
    
    /** @return DriverInterface */
    public function getDriverBy($id)
    {
        if (! array_key_exists($id, $this->drivers)) {
            $factory = $this->getFactory();
            $this->drivers[$id] = $factory($this->options['drivers'][$id]);
        }
        
        return $this->drivers[$id];
    }
    
    public function getFactory()
    {
        return function(array $options) {
            $className = $options['type'];
            return new $className($options['options']);
        };
    }
    
    public function prepare($sql, array $params = null)
    {
        if ($this->logger) {
            $this->logger->debug("[SQL.PREPARE] {$sql} " . json_encode($params));
        }
        $sql = preg_replace('#{([^}]+)}#', $this->options['dbPrefix'] . '$1', $sql);
        return $this->getDriver()->prepare($sql, $params);
    }
    
    public function query($sql, array $params = null)
    {
        if ($this->logger) {
            $this->logger->debug('[SQL.QUERY] ' . $sql . ' ' . json_encode($params));
        }
        return $this->prepare($sql)->exec($params);
    }
    
    public function exec($sql)
    {
        $sql = preg_replace('#{([^}]+)}#', $this->options['dbPrefix'] . '$1', $sql);
        
        if ($this->logger) {
            $this->logger->debug("[SQL.EXEC] {$sql}");
        }
        
        return $this->getDriver()->exec($sql);
    }
    
    public function listTableNames()
    {
        return $this->getDriver()->listTableNames();
    }
    
    public function getLastInsertedId()
    {
        return $this->getDriver()->getLastInsertedId();
    }
    
    public function escape($str)
    {
        return $this->getDriver()->escape($str);
    }
    
    public function getLastError()
    {
        return $this->getDriver()->getLastError();
    }

    public function getAffectedCount()
    {
        return $this->getDriver()->getAffectedCount();
    }
}
