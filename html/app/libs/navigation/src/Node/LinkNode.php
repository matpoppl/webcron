<?php

namespace matpoppl\Navigation\Node;

class LinkNode extends AbstractNode
{
    private $label;
    
    public function getUri()
    {
        return $this->getAttributes()->get('href');
    }
    
    public function setUri($uri)
    {
        return $this->getAttributes()->set('href', $uri);
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}
