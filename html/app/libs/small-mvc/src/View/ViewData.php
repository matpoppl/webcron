<?php

namespace matpoppl\SmallMVC\View;

use matpoppl\PathManager\PathManagerInterface;

/**
 * @property Helper\EscapeHelper $escape
 * @property Helper\FlashMessengerHelper $flashMessenger
 * @property Helper\MetaHelper $meta
 * @property Helper\RouteHelper $route
 * @property Helper\TranslateHelper $translate
 * @property Helper\NavHelper $nav
 * @property Helper\AssetsHelper $assets
 * @method string route($name, array $params = null)
 */
class ViewData implements \ArrayAccess
{
    /** @var array */
    private $data;
    /** @var string[] */
    private $tasks = [];
    /** @var Helper\Container */
    private $container;
    /** @var PathManagerInterface */
    private $pathManager;
    
    public function __construct(Helper\Container $container, PathManagerInterface $pathManager, array $data = null)
    {
        $this->container = $container;
        $this->pathManager = $pathManager;
        $this->data = null === $data ? [] : $data;
    }
    
    public function addData(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    public function has(string $id)
    {
        return array_key_exists($id, $this->data);
    }

    public function get(string $id, $default = null)
    {
        return array_key_exists($id, $this->data) ? $this->data[$id] : $default;
    }

    public function set(string $id, $value)
    {
        $this->data[$id] = $value;
        return $this;
    }

    public function remove(string $id)
    {
        if (array_key_exists($id, $this->data)) {
            unset($this->data[$id]);
        }
    }

    public function offsetExists($offset) : bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset) : mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) : void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) : void
    {
        $this->remove($offset);
    }
    
    public function render($_pathname)
    {
        $render = function($_pathname, $view) {
            ob_start();
            require $_pathname;
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        };
        
        $this->tasks[] = $_pathname;
        
        $ret = '';
        
        while ($template = array_shift($this->tasks)) {
            $pathname = $this->pathManager->getPathname($template);
            
            if (null === $pathname) {
                throw new \InvalidArgumentException('Template dont exists `'.$template.'`');
            }
            
            if (! is_file($pathname)) {
                throw new \InvalidArgumentException('Template file dont exists `'.$pathname.'`');
            }
            
            $this->data['_content'] = $ret;
            $ret = $render($pathname, $this);
        }
        
        return $ret;
    }
    
    public function extends($name)
    {
        $this->tasks[] = $name;
        return '';
    }
    
    public function __get($name)
    {
        return $this->getHelper($name);
    }
    
    public function __call($name, array $args)
    {
        $helper = $this->getHelper($name);
        return $helper(...$args);
    }
    
    public function getHelper($name)
    {
        return $this->container->get('view.helper.' . $name);
    }
}
