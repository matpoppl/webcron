<?php

namespace matpoppl\DBAL;

interface StatementInterface extends \Countable, \Traversable
{
    public function getStatement();
    
    public function exec(array $params = null);
}
