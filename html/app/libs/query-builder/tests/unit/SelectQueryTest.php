<?php

namespace matpoppl\QueryBuilder;

use PHPUnit\Framework\TestCase;

class SelectQueryTest extends TestCase
{
    public function testWhereLimit()
    {
        $select = new SelectQuery();
        
        $select->from(['f' => 'foo'], ['id', 'bar'])->where([
            'f.`bar`=?' => 11,
        ])->limit(10, 20);
        
        $qb = new QueryBuilder();
        
        self::assertEquals('SELECT f.`id`, f.`bar` FROM `foo` AS f WHERE f.`bar`=? LIMIT 20,10', $qb->buildSelect($select)->getSql());
        self::assertEquals([11], $select->getParams());
    }
    
    public function testJoins()
    {
        $select = new SelectQuery();
        
        $select->from(['f' => 'foo'], ['luz' => 'name'])
        ->join(['b' => 'bar'], 'b.`parent`=f.`id`', ['qux' => 'id']);
        
        $qb = new QueryBuilder();
        
        self::assertEquals('SELECT f.`name` AS luz, b.`id` AS qux FROM `foo` AS f JOIN `bar` AS b ON (b.`parent`=f.`id`)', $qb->buildSelect($select)->getSql());
    }
}
