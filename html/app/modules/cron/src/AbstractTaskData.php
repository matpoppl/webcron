<?php

namespace matpoppl\Cron;

abstract class AbstractTaskData
{
    /** @var string */
    public $params;
    
    /** @var array */
    private $_params = null;
    
    /**
     * @throws \UnexpectedValueException
     * @return array
     */
    public function getParams()
    {
        if (null === $this->_params) {
            $this->_params = strlen($this->params) > 0 ? json_decode($this->params, true) : [];
            if (! is_array($this->_params)) {
                throw new \UnexpectedValueException('Params json parse error');
            }
        }
        
        return $this->_params;
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        if (null === $this->_params) {
            $this->getParams();
        }
        
        return array_key_exists($key, $this->_params) ? $this->_params[$key] : $default;
    }
    
    /**
     * 
     * @param array $params
     * @return static
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        $this->params = json_encode($params);
        return $this;
    }
}
