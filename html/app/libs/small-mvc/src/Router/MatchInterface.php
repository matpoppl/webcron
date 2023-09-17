<?php

namespace matpoppl\SmallMVC\Router;

interface MatchInterface
{
    /** @return string */
    public function getController();
    /** @return string */
    public function getAction();
    /** @return array */
    public function getParams();
    public function getParam($name);
    public function setParam($name, $val);
}
