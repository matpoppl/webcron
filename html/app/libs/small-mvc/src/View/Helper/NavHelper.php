<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;
use matpoppl\Navigation\ItemFactory;
use matpoppl\Navigation\Navigation;
use matpoppl\Navigation\Node\ContainerInterface as NavContainerInterface;
use matpoppl\Navigation\Renderer;
use matpoppl\Navigation\View\BreadcrumbsView;
use matpoppl\SmallMVC\Router\Router;
use matpoppl\Navigation\Node\TreeNodeInterface;

class NavHelper extends AbstractHelper
{
    /** @var Navigation[] */
    private $menus = [];
    /** @var Router */
    private $request;
    /** @var ItemFactory */
    private $itemFactory;
    /** @var BreadcrumbsView */
    private $breadcrumbs = false;
    
    public function __construct($request, ItemFactory $itemFactory)
    {
        $this->request = $request;
        $this->itemFactory = $itemFactory;
    }
    
    public function __invoke($menuId = null)
    {
        if (null === $menuId) {
            return $this;
        }
        
        return $this->hasMenu($menuId) ? $this->getMenu($menuId) : null;
    }
    
    public function hasMenu($menuId)
    {
        return array_key_exists($menuId, $this->menus);
    }
    
    /**
     * @param string $menuId
     * @return Navigation
     */
    public function getMenu($menuId)
    {
        if (! $this->hasMenu($menuId)) {
            throw new \DomainException('Menu `'.$menuId.'` dont exists');
        }
        
        return $this->menus[$menuId];
    }
    
    /**
     * @param string $menuId
     * @return Navigation
     */
    public function setMenu($menuId, NavContainerInterface $nav)
    {
        $this->menus[$menuId] = $nav;
        return $this;
    }
    
    public function createMenu($menuId, array $options)
    {
        $this->setMenu($menuId, new Navigation($this->itemFactory, $options));
        return $this->menus[$menuId];
    }
    
    public function populateBreadcrumbs(NavContainerInterface $nav)
    {
        $uri = $this->request->getUri()->getPath();
        
        /** @var TreeNodeInterface $node */
        $node = $nav->findByUri($uri);
        
        if (null === $node) {
            return null;
        }
        
        $node->getItem()->getAttributes()->set('aria-current', 'page');
        
        $ret = [$node];
        while ($node = $node->getParentNode()) {
            $node->getItem()->getAttributes()->addClass('on-path');
            $node->getLink()->getOptions()->set('on-path', true);
            $ret[] = $node;
        }
        
        return new BreadcrumbsView(array_reverse($ret));
    }
    
    /** @return BreadcrumbsView */
    public function getBreadcrumbs()
    {
        if (false !== $this->breadcrumbs) {
            return $this->breadcrumbs;
        }
        
        foreach ($this->menus as $nav) {
            $bs = $this->populateBreadcrumbs($nav);
            
            if (null === $bs) {
                continue;
            }
            
            $this->breadcrumbs = $bs;
        }
        
        return $this->breadcrumbs;
    }
    
    public function renderMenu(NavContainerInterface $menu, array $attributes = null)
    {
        $this->populateBreadcrumbs($menu);
        
        $renderer = new Renderer($this->itemFactory);
        return $renderer->renderMenu($menu instanceof Navigation ? $menu->getRoot() : $menu, $attributes);
    }
    
    public static function create(ContainerInterface $container, ...$args)
    {
        $ret = new static($container->get('request'), new ItemFactory($container->get('router')));
        
        $config = $container->get('config');
        
        if (isset($config['menus'])) {
            foreach ($config['menus'] as $menuName => $menuOpts) {
                
                if (isset($menuOpts['items'])) {
                    $ret->createMenu($menuName, $menuOpts);
                } else {
                    $ret->createMenu($menuName, [
                        'items' => $menuOpts
                    ]);
                }
                
            }
        }
        
        return $ret;
    }
}
