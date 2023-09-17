<?php

namespace matpoppl\SmallMVC\Utils;

class JsonPartsWriter implements LogWriterInterface
{
    /** @var string */
    private $pathname;
    
    public function __construct($pathname)
    {
        $this->pathname = $pathname;
    }
    
    public function write(array $logs)
    {
        $handle = fopen($this->pathname, 'w+');
        
        if (! $handle) {
            return -1;
        }
        
        $ret = fwrite($handle, json_encode($logs));
        $ret += fwrite($handle, ",\n");
        
        fclose($handle);
        
        return $ret;
    }
    
    /**
     * 
     * @throws \UnexpectedValueException
     * @return array
     */
    public function getContents()
    {
        $handle = fopen($this->pathname, 'r');
        
        if (! $handle) {
            return;
        }
        
        $data = stream_get_contents($handle);
        fclose($handle);
        
        $json = json_decode($data);
        
        $err = json_last_error();
        
        if (JSON_ERROR_NONE === json_last_error()) {
            return $json;
        }
        
        throw new \UnexpectedValueException(json_last_error_msg(), $err);
    }
}
