<?php

namespace matpoppl\Translate;

use matpoppl\DBAL\DBALInterface;

class PhpArraySource
{
    private $locale;
    private $domain;
    private $data;
    
    private $dir;
    
    public function __construct(string $locale, string $defaultDomain, array $data)
    {
        $this->locale = $locale;
        $this->domain = $defaultDomain;
        $this->data = $data;
    }
    
    public function translate(string $msgid, array $params = null, string $domain = null, string $locale = null)
    {
        return array_key_exists($msgid, $this->data) ? $this->data[$msgid] : $msgid;
    }
    
    public function read(string $locale, string $domain)
    {
        $filename = "{$locale}-{$domain}.php";
        $pathname = $this->dir . $filename;
        
        if (! is_readable($pathname)) {
            throw new \InvalidArgumentException('File dont exists');
        }
        
        $data = require($pathname);
        
        if (! is_array($data)) {
            throw new \UnexpectedValueException();
        }
        
        return $data;
    }
    
    public function write(string $locale, string $domain, array $data)
    {
        $filename = "{$locale}-{$domain}.php";
        $pathname = $this->dir . $filename;
        
        if (! is_writable($pathname)) {
            throw new \InvalidArgumentException('File dont exists');
        }
        
        file_put_contents($pathname, '<?php return ' . var_export($this->data, true) . ';');
        
        return $this;
    }
}
