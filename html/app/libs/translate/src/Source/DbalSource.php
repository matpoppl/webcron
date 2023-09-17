<?php

namespace matpoppl\Translate;

use matpoppl\DBAL\DBALInterface;

class DbalSource
{
    /** @var DBALInterface */
    private $dbal;
    
    /** @var string[][] */
    private $domain = [];
    
    public function __construct(DBALInterface $dbal)
    {
        $this->dbal = $dbal;
    }
    
    public function load()
    {
        $sql = 'SELECT `domain`, `msgid`, `value` FROM `{translations}` WHERE `locale`=?';
        foreach ($this->dbal->query($sql, [$locale]) as $row) {
            $this->domain[ $row['domain'] .'.'. $row['msgid'] ] = $row['value'];
        }
        return $this->domain;
    }
    
    public function get(string $domain, string $locale)
    {
    }
}
