<?php

namespace matpoppl\Hydrator;

class SampleMethodClass
{
    private $foo = null;
    private $barBaz = null;
    private $qux = null;
    private $xyZy = null;
    
    public function getFoo()
    {
        return $this->foo;
    }
    
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    
    public function getBarBaz()
    {
        return $this->barBaz;
    }
    
    public function setBarBaz($barBaz)
    {
        $this->barBaz = $barBaz;
    }
    
    public function getQUX()
    {
        return $this->qux;
    }
    
    public function setQUX($qux)
    {
        $this->qux = $qux;
    }
    
    public function setXYZy($xyZy)
    {
        $this->xyZy = $xyZy;
    }
    
    public function getXYZy()
    {
        return $this->xyZy;
    }
}
