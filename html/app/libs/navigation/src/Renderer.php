<?php
namespace matpoppl\Navigation;

use matpoppl\Navigation\Node\TreeNode;

class Renderer
{
    /** @var ItemFactory */
    private $itemFactory;
    
    public function __construct(ItemFactory $itemFactory)
    {
        $this->itemFactory = $itemFactory;
    }
    
    public function isHidden($item)
    {
        return $item->getOptions()->get('hidden');
        //return ! $acl->check('read');
    }
    
    public function renderMenu(TreeNode $root, array $attributes = null)
    {
        if (count($root) < 1) {
            return '';
        }
        
        $ret = '';

        foreach ($root->getIterator() as $child) {
            
            $item = $child->getItem();
            
            if (null !== $item && $this->isHidden($item)) {
                continue;
            }

            $link = $child->getLink();
            $itemAttrs = (null === $item) ? '' : $item->getAttributes()->render();
            
            $expand = true;
            switch ($link->getOptions()->get('expand')) {
                case 'on-path':
                    $expand = $link->getOptions()->get('on-path', false);
                    break;
            }
            
            $submenu = $expand ? $this->renderMenu($child) : '';
            
            $ret .= '<li'.$itemAttrs.'><a'.$link->getAttributes().'>' . $link->getLabel() . '</a>' . $submenu . '</li>';
        }
        
        if (null === $attributes) {
            $list = $root->getList();
        } else {
            $list = $this->itemFactory->createList(['attributes' => $attributes]);
        }
        
        $listAttrs = $list ? $list->getAttributes()->render() : '';
        
        return '<ul'.$listAttrs.'>' . $ret . '</ul>';
    }
}
