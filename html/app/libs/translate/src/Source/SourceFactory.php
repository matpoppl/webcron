<?php

namespace matpoppl\Translate\Source;

use matpoppl\ServiceManager\ServiceManagerInterface;

class SourceFactory
{
    /** @var ServiceManagerInterface */
    private $container;
    
    public function __construct(ServiceManagerInterface $container)
    {
        $this->container = $container;
    }
    
    public function create(array $options)
    {
        $type = $options['type'] ?? null;
        
        if ($this->container->has($type)) {
            return $this->container->get($type);
        }
        
        if (! class_exists($type)) {
            throw new \UnexpectedValueException('Source class dont exists ' . $type);
        }
        
        unset($options['type']);
        
        switch ($type) {
            case GettextSource::class:
                
                $options['domains'] = $options['locales'][ $options['locale'] ];
                
                return new $type($options);
        }
        
        return new $type($options);
    }
}
