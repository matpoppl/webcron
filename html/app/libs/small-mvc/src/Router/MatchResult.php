<?php

namespace matpoppl\SmallMVC\Router;

class MatchResult implements MatchInterface
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function getAction()
    {
        return $this->params['action'] ?? null;
    }

    public function getController()
    {
        return $this->params['controller'] ?? null;
    }

    public function getParams()
    {
        return $this->params;
    }
    
    public function setParam($name, $val)
    {
        $this->params[$name] = $val;
        return $this;
    }
    
    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }
}
