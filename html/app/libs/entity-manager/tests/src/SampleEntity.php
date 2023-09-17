<?php

namespace matpoppl\EntityManager;

class SampleEntity implements EntityInterface
{
    private $_isNewRecord = true;
    
    private $foo;
    private $barBaz;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
    
    public function getBarBaz()
    {
        return $this->barBaz;
    }
    
    public function setFoo($foo)
    {
        $this->foo = $foo;
        return $this;
    }
    
    public function setBarBaz($barBaz)
    {
        $this->barBaz = $barBaz;
        return $this;
    }
    
    public function exchangeArray(array $data)
    {
        $this->foo = $data['foo'];
        $this->barBaz = $data['bar_baz'];
    }
    
    public function toArray()
    {
        return [
            'foo' => $this->foo,
            'bar_baz' => $this->barBaz,
        ];
    }
}
