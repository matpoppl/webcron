<?php

namespace matpoppl\DBAL;

class BulkTool
{
    /** @var DBALInterface */
    private $dbal;
    
    public function __construct(DBALInterface $dbal)
    {
        $this->dbal = $dbal;
    }
    
    public function execFromGlob(string $globPattern)
    {
        foreach (glob($globPattern) as $file) {
            $queries = file_get_contents($file);
            $this->execQueries($queries);
        }
    }
    
    public function execQueries(string $queries)
    {
        $dbPrefix = $this->dbal->getOption('dbPrefix');
        
        $queries = str_replace('{DBPREFIX}', $dbPrefix, $queries);
        
        foreach (preg_split('#\s?;\s?#', $queries) as $sql) {
            assert($this->dbal->exec($sql), '[SQL ERROR]: ' . $sql);
        }
    }
}
