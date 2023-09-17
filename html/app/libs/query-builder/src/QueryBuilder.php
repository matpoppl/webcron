<?php

namespace matpoppl\QueryBuilder;

class QueryBuilder implements QueryBuilderInterface
{
    public function select() : SelectInterface
    {
        return new SelectQuery();
    }
    
    public function update() : UpdateInterface
    {
        return new UpdateQuery();
    }
    
    public function insert() : InsertInterface
    {
        return new InsertQuery();
    }
    
    public function delete() : DeleteInterface
    {
        return new DeleteQuery();
    }
    
    public function build($query) : QueryInterface
    {
        if ($query instanceof SelectInterface) {
            return $this->buildSelect($query);
        }
        
        if ($query instanceof InsertInterface) {
            return $this->buildInsert($query);
        }
        
        if ($query instanceof UpdateInterface) {
            return $this->buildUpdate($query);
        }
        
        if ($query instanceof DeleteInterface) {
            return $this->buildDelete($query);
        }
        
        throw new \UnexpectedValueException('Unsupported query type');
    }
    
    public function buildSelect(SelectInterface $query) : QueryInterface
    {
        $params = null;
        $tn = $query->getTableName();
        $columns = $query->getColumns() ?: '*';
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        
        $where = '';
        if ($conds = $query->getConditions()) {
            $where = implode(' AND ', $conds);
            $params = $query->getValues();
        }
        
        $group = '';
        $order = '';

        list($offset, $limit) = $query->getLimit();
        
        $sql = "SELECT {$columns} FROM {$tn}";
        
        
        foreach ($query->getJoins() as $join) {
            $sql .= " {$join['joined_table']} {$join['table_factor']} {$join['join_specification']}";
        }
        
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }
        
        if ($group) {
            $sql .= ' GROUP BY ' . $group;
        }
        
        if ($order) {
            $sql .= ' ORDER BY ' . $where;
        }
        
        if ($offset > 0 && $limit > 0) {
            $sql .= " LIMIT {$offset},{$limit}";
        } else if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        return $query->setSql($sql)->setParams($params);
    }
    
    public function buildInsert(InsertInterface $query) : QueryInterface
    {
        $tn = $query->getTableName();
        $tmp = null;
        $columns = null;
        $values = [];
        $params = [];
        
        foreach ($query->getValues() as $i => $rowValues) {
            if (null === $columns) {
                $keys = array_keys($rowValues);
                // :name$, later $ will be reaplaces with $i
                $tmp = ':'.implode('$, :', $keys) . '$';
                $columns = '`'.implode('`, `', $keys).'`';
            }
            $values[] = '(' . str_replace('$', $i, $tmp) . ')';
            foreach ($rowValues as $key => $val) {
                $params[':' . $key . $i] = $val;
            }
        }
        
        $values = implode(', ', $values);
        
        // Wrap {tableName} with {} for later DB_PREFIX injection
        $sql = "INSERT INTO {$tn} ({$columns}) VALUES {$values}";
        
        return $query->setSql($sql)->setParams($params);
    }
    
    public function buildUpdate(UpdateInterface $query) : QueryInterface
    {
        $tn = $query->getTableName();
        
        $params = [];
        
        $set = [];
        foreach ($query->getAssignments() as $col => $val) {
            $matched = null;
            if (preg_match_all('#(\?|\:\w+)#', $col, $matched) > 0) {
                throw new \Exception('Not implemented');
            } else {
                $set[] = "`{$col}`=:{$col}";
                $params[':' . $col] = $val;
            }
        }
        
        $where = [];
        foreach ($query->getConditions() as $condition => $val) {

            $matched = null;
            if (preg_match_all('#(\?|\:\w+)#', $condition, $matched) > 0) {
                throw new \Exception('Not implemented');
            } else {
                $where[] = "`{$condition}`=:{$condition}";
                $params[':' . $condition] = $val;
            }
        }
        
        $set = implode(', ', $set);
        $where = implode(', ', $where);
        
        // Wrap {tableName} with {} for later DB_PREFIX injection
        $sql = "UPDATE {$tn} SET {$set} WHERE {$where}";
        
        return $query->setSql($sql)->setParams($params);
    }
    
    public function buildDelete(DeleteInterface $query) : QueryInterface
    {
        $tn = $query->getTableName();
        
        $params = [];
        
        $where = [];
        foreach ($query->getConditions() as $condition => $val) {
            
            $matched = null;
            if (preg_match_all('#(\?|\:\w+)#', $condition, $matched) > 0) {
                throw new \Exception('Not implemented');
            } else {
                $where[] = "`{$condition}`=:{$condition}";
                $params[':' . $condition] = $val;
            }
        }
        
        $where = implode(', ', $where);
        
        // Wrap {tableName} with {} for later DB_PREFIX injection
        $sql = "DELETE FROM {$tn} WHERE {$where}";
        
        return $query->setSql($sql)->setParams($params);
    }
    
    public function populateWhere(ConditionQueryInterface $query, $where)
    {
        if (null === $where) {
            return $query;
        }
        
        if (! is_array($where)) {
            throw new \UnexpectedValueException('Unsupported where type');
        }
        
        foreach ($where as $key => $val) {
            if (is_int($key)) {
                $query->where($val);
                continue;
            }
            
            $query->where($key, $val);
        }
        
        return $query;
    }
}
