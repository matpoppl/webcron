<?php 

namespace matpoppl\Navigation\View;

use matpoppl\Navigation\Node\TreeNodeInterface;
use matpoppl\Navigation\Node\Attributes;

class BreadcrumbsView
{
    /** @var TreeNodeInterface */
    private $items;
    
    public function __construct(array $items)
    {
        $this->items = $items;
    }
    
    public function render(array $attributes = null)
    {
        $ret = '';
        
        foreach ($this->items as $child) {

            $link = $child->getLink();

            if (null === $link) {
                continue;
            }
            
            $ret .= '<li><a'.$link->getAttributes().'>' . $link->getLabel() . '</a></li>';
        }
        
        $listAttrs = new Attributes($attributes);
        
        return '<ul'.$listAttrs.'>' . $ret . '</ul>';
    }
}
