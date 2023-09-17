<?php

namespace matpoppl\Navigation\Node;

use matpoppl\Navigation\Utils\ArrayObject;

function escape(string $str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function html_classes(array $classes)
{
    $ret = '';
    
    foreach ($classes as $key => $val) {
        
        if (null === $val || false === $val) {
            continue;
        }
        
        if (is_int($key)) {
            $ret .= ' ' . escape($val);
            continue;
        } else if (is_string($key)) {
            $ret .= ' ' . escape($key);
            continue;
        }
    }
    
    return $ret;
}

class Attributes extends ArrayObject
{
    public function addClass($newClass)
    {
        if (is_string($newClass)) {
            $tmp = explode(' ', $newClass);
            $newClass = array_combine($tmp, array_fill(0, count($tmp), true));
        }
        
        $class = $this->get('class');
        
        if (is_array($class)) {
            $this->set('class', array_merge($class, $newClass));
        } else {
            $newClass[$class] = true;
            $this->set('class', $newClass);
        }
        
        return $this;
    }
    
    public function render()
    {
        $ret = '';
        
        foreach ($this->getIterator() as $key => $val) {
            if (null === $val || false === $val) {
                continue;
            }
            
            switch (gettype($val)) {
                case 'bool':
                case 'boolean':
                    $ret .= ' ' . escape($key);
                    break;
                case 'array':
                    if ($val) {
                        $ret .= ' ' . escape($key) . '="' . html_classes($val) . '"';
                    }
                    break;
                case 'object':
                    if ($val) {
                        $ret .= ' ' . escape($key) . '="' . escape(json_encode($val)) . '"';
                    }
                    break;
                default:
                    $ret .= ' ' . escape($key) . '="' . escape($val) . '"';
                    break;
            }
        }
        
        return $ret;
    }
    
    public function __toString()
    {
        return $this->render();
    }
    
    public static function escape(string $str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
