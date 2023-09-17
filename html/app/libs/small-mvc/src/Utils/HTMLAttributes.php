<?php

namespace matpoppl\SmallMVC\Utils;

use matpoppl\Form\Utils\ArrayObject;

function escape($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

class HTMLAttributes extends ArrayObject
{

    public function render()
    {
        $ret = '';

        foreach ($this as $name => $value) {
            if (null === $value) {
                continue;
            }

            switch (gettype($value)) {
                case 'bool':
                case 'boolean':
                    if ($value) {
                        $ret .= ' ' . escape($name);
                    }
                    break;
                case 'array':
                    $ret .= ' ' . escape($name) . '="' . escape(implode(' ', $value)) . '"';
                    break;
                case 'object':
                    $ret .= ' ' . escape($name) . '="' . escape(json_encode($value)) . '"';
                    break;
                default:
                    $ret .= ' ' . escape($name) . '="' . escape($value) . '"';
                    break;
            }
        }

        return $ret;
    }
    
    public function without(...$keys)
    {
        return new static(array_diff_key($this->getArrayCopy(), array_combine($keys, $keys)));
    }
    
    public function __toString()
    {
        return $this->render();
    }

    public static function create(array $attributes = null)
    {
        return new static($attributes);
    }
}
