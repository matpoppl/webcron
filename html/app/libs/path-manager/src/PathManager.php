<?php

namespace matpoppl\PathManager;

class PathManager implements PathManagerInterface
{
    /** @var LocationInterface[] */
    private $locations = [];
    /** @var string|NULL */
    private $defaultLocation = null;
    
    public function __construct(array $options)
    {
        if (isset($options['locations'])) {
            $this->setLocations($options['locations']);
        }
    }
    
    public function setLocations(array $locations)
    {
        $this->locations = [];
        foreach ($locations as $alias => $location) {
            $this->addLocation($alias, $location);
        }
        return $this;
    }
    
    /**
     * 
     * @param string $alias
     * @param string|LocationInterface $location
     * @return static
     */
    public function addLocation($alias, $location)
    {
        if ($location instanceof LocationInterface) {
            $this->locations[$alias] = $location;
            return $this;
        }
        
        $pos = strpos($location, ':');
        
        // exclude drive letter Windows paths
        if ($pos > 1) {
            $parent = substr($location, 0, $pos);
            $location = substr($location, $pos + 1);
            $this->locations[$alias] = $this->getLocation($parent)->append($location);
        } else {
            $this->locations[$alias] = new Location($location);
        }
        
        return $this;
    }
    
    /**
     * 
     * @param string $alias
     * @throws \UnexpectedValueException
     * @return LocationInterface
     */
    public function getLocation(string $alias) : LocationInterface
    {
        if (! array_key_exists($alias, $this->locations)) {
            throw new \UnexpectedValueException("Location `{$alias}` not found");
        }
        
        return $this->locations[$alias];
    }
    
    /**
     *
     * @param string $location
     * @throws \UnexpectedValueException
     * @return LocationInterface
     */
    public function get(string $location) : LocationInterface
    {
        $pos = strpos($location, ':');
        
        if (false === $pos) {
            if (null === $this->defaultLocation) {
                throw new \UnexpectedValueException('Location alias required');
            }
            $alias = $this->defaultLocation;
        } else {
            $alias = substr($location, 0, $pos);
            $location = substr($location, $pos + 1);
        }
        
        return $this->getLocation($alias)->append($location);
    }
    
    public function getPathname(string $location): string
    {
        return $this->get($location)->getPathname();
    }
}
