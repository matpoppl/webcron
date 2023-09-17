<?php

namespace matpoppl\DBAL\MySQL;

use matpoppl\DBAL\DriverInterface;

class MySQLDriver implements DriverInterface
{
    /** @var resource */
    private $resource = null;
    
    /** @var array */
    private $options;
    
    public function __construct(array $options)
    {
        $this->options = $options;
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    public function getDriver()
    {
        if (null !== $this->resource) {
            return $this->resource;
        }
        
        $this->options += [
            'host' => null,
            'port' => null,
            'dbname' => null,
            'user' => null,
            'pass' => null,
            'charset' => null,
        ];
        
        if (array_key_exists('dsn', $this->options)) {
            $opts = parse_url($this->options['dsn']);
            $dbname = ltrim($opts['path'], '/');
            unset($opts['path'], $this->options['dsn']);
            $this->options = array_merge($this->options, $opts);
            $this->options['dbname'] = $dbname;
        }
        
        $res = mysql_connect(
            $this->options['host'] . ($this->options['port'] ? ':' . $this->options['port'] : ''),
            $this->options['user'],
            $this->options['pass'],
            false,
            $this->options['flags']
        );
        
        if (! $res) {
            throw new \UnexpectedValueException('Database connect error');
        }
        
        if (! mysql_set_charset($this->options['charset'], $res)) {
            throw new \UnexpectedValueException('Database charset set error');
        }
        
        
        if (! mysql_select_db($this->options['dbname'], $res)) {
            throw new \UnexpectedValueException('Database select error');
        }
        
        $this->resource = $res;
        
        return $this->resource;
    }
    
    public function close()
    {
        if (null !== $this->resource) {
            mysql_close($this->resource);
        }
        
        $this->resource = null;
        
        return $this;
    }
    
    public function getLastInsertedId()
    {
        return mysql_insert_id($this->getDriver());
    }
    
    public function getAffectedCount()
    {
        return mysql_affected_rows($this->getDriver());
    }
    
    public function getLastError()
    {
        return [mysql_errno($this->getDriver()), mysql_error($this->getDriver())];
    }
    
    public function listTableNames()
    {
        //$sql = 'SHOW TABLES [FROM db_name] [LIKE 'pattern']';
        $ret = [];
        foreach ($this->query($sql) as $row) {
            $ret[] = $row['table_name'];
        }
        return $ret;
    }
    
    public function prepare($sql, array $params = null)
    {}

    public function query($sql, array $params = null)
    {}

    public function escape($str)
    {
        return mysql_real_escape_string($str, $this->getDriver());
    }

    public function exec($sql)
    {
        
    }
}
