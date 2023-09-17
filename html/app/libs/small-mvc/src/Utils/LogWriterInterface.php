<?php

namespace matpoppl\SmallMVC\Utils;

interface LogWriterInterface
{
    /**
     * 
     * @param array $logs
     * @return int Number of written bytes. Negative on error
     */
    public function write(array $logs);
    
    /** @return string */
    public function getContents();
}
