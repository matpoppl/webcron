<?php

namespace matpoppl\SmallMVC\View\Helper;

class MetaHelper extends AbstractHelper
{
    /** @var string */
    private $title;
    
    public function __invoke($name)
    {
        return $this;
    }
    
    public function title($title = null)
    {
        if (null === $title) {
            return $this->title;
        }
        
        $this->title = $title;
        
        return $this;
    }
}