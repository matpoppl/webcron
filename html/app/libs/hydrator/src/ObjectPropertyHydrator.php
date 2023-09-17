<?php

namespace matpoppl\Hydrator;

class ObjectPropertyHydrator extends AbstractHydrator
{
    public function hydrate(array $data, $obj)
    {
        $naming = $this->setterNamingStrategy;
        
        foreach ($data as $key => $val) {
            $propName = $naming ? $naming($key) : $key;
            
            if (property_exists($obj, $propName)) {
                // match PROP...
                $obj->{$propName} = $val;
            }
        }
    }
    
    public function extract($obj)
    {
        $naming = $this->getterNamingStrategy;
        
        $ret = [];
        foreach (get_object_vars($obj) as $prop => $val) {
            if ('_' === $prop[0]) {
                continue;
            }
            
            $key = $naming ? $naming($prop) : $prop;
            
            $ret[$key] = $val;
            
        }
        return $ret;
    }
}
