<?php

namespace matpoppl\QueryBuilder;

interface QueryBuilderInterface
{
    public function select() : SelectInterface;
    public function update() : UpdateInterface;
    public function insert() : InsertInterface;
    public function delete() : DeleteInterface;
    public function build($query) : QueryInterface;
    public function populateWhere(ConditionQueryInterface $query, $where);
}
