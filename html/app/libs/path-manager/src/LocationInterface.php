<?php

namespace matpoppl\PathManager;

interface LocationInterface
{
    /**
     * 
     * @param string $location
     * @return LocationInterface
     */
    public function append(string $location);
    
    /** @return string */
    public function getPathname();
}
