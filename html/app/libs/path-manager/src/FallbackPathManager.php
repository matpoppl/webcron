<?php

namespace matpoppl\PathManager;

class FallbackPathManager implements PathManagerInterface
{
    /** @var PathManagerInterface */
    private $pathManager;
    /** @var LocationInterface */
    private $defaultLocation;
    
    public function __construct(PathManagerInterface $pathManager, LocationInterface $defaultLocation)
    {
        $this->pathManager = $pathManager;
        $this->defaultLocation = $defaultLocation;
    }
    
    public function getPathname(string $location): string
    {
        $pos = strpos($location, ':');
        
        if (false === $pos) {
            return $this->defaultLocation->append($location)->getPathname();
        }
        
        return $this->pathManager->getPathname($location);
    }
}
