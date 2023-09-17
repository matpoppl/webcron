<?php
namespace matpoppl\QueryBuilder;

use matpoppl\DBAL\StatementInterface;

abstract class AbstractQuery implements QueryInterface
{

    private $sql;

    private $params = null;

    public function getSql()
    {
        return $this->sql;
    }

    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params = null)
    {
        $this->params = $params;
        return $this;
    }
}
