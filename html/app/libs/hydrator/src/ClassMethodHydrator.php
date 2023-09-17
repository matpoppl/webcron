<?php

namespace matpoppl\Hydrator;

use ReflectionMethod;

class ClassMethodHydrator extends AbstractHydrator
{
    public function hydrate(array $data, $obj)
    {
        $naming = $this->setterNamingStrategy;
        
        foreach ($data as $key => $val) {
            $method = 'set' . ($naming ? $naming($key) : $key);

            if (method_exists($obj, $method)) {
                $obj->{$method}($val);
            }
        }
    }
    
    public function extract($obj)
    {
        $naming = $this->getterNamingStrategy;
        
        $ret = [];
        
        $ref = new \ReflectionClass(get_class($obj));
        
        $filter = ReflectionMethod::IS_PUBLIC;
        foreach ($ref->getMethods($filter) as $method) {
            /** @var ReflectionMethod $method */
            $methodName = $method->getName();
            
            if (0 !== strpos($methodName, 'get') || $method->isStatic() || $method->getNumberOfRequiredParameters() > 0) {
                continue;
            }
        
            $key = substr($methodName, 3);
            $key = $naming ? $naming($key) : $key;
            
            $ret[$key] = $obj->{$methodName}();
        }
        return $ret;
    }
}
