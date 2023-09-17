<?php

namespace matpoppl\InputFilter;

use matpoppl\ServiceManager\ServiceManagerInterface;

class InputFilterBuilder
{
    /** @var ServiceManagerInterface */
    private $sm;
    
    public function __construct(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
    }
    
    public function createInputFilter(array $options)
    {
        $if = new InputFilter($this->sm, $options);
        
        return $if;
    }
    
    /** @return ServiceManagerInterface */
    public function getServiceManager()
    {
        return $this->sm;
    }
}
