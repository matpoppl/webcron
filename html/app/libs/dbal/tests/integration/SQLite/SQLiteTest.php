<?php

namespace matpoppl\DBAL\SQLite;

use PHPUnit\Framework\TestCase;

class SQLiteTest extends TestCase
{
    public function testCreateTable()
    {
        $dbal = new SQLiteDriver([
            'dsn' => ':memory:'
        ]);
        
        self::assertEquals([], $dbal->listTableNames());
        
        self::assertTrue($dbal->exec('CREATE TABLE `tb1` ( `id` INTEGER, `name` TEXT, PRIMARY KEY (`id`) )'));
        
        self::assertEquals(['tb1'], $dbal->listTableNames());
        
        self::assertTrue($dbal->exec('DROP TABLE `tb1`'));
        
        self::assertEquals([], $dbal->listTableNames());
    }
    
    public function testSelect()
    {
        $dbal = new SQLiteDriver([
            'dsn' => ':memory:'
        ]);
        
        self::assertTrue($dbal->exec('CREATE TABLE `tb1` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `name` TEXT )'));
        
        $dbal->exec('INSERT INTO `tb1` (`name`) VALUES (\'foo\'),(\'bar\')');
        
        $result = $dbal->query('SELECT * FROM `tb1`');
        
        self::assertEquals(2, count($result));
        
        self::assertEquals([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ], iterator_to_array($result));
        
        self::assertTrue($dbal->exec('DROP TABLE `tb1`'));
    }
    
    public function testSelectParams()
    {
        $dbal = new SQLiteDriver([
            'dsn' => ':memory:'
        ]);
        
        self::assertTrue($dbal->exec('CREATE TABLE `tb1` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `name` TEXT )'));
        
        $dbal->exec('INSERT INTO `tb1` (`name`) VALUES (\'foo\'),(\'bar\'),(\'baz\'),(\'qux\')');
        
        //
        
        $result = $dbal->query('SELECT * FROM `tb1`');
        
        self::assertEquals(4, count($result));
        
        self::assertEquals([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
            ['id' => 3, 'name' => 'baz'],
            ['id' => 4, 'name' => 'qux'],
        ], iterator_to_array($result));
        
        //
        
        $result = $dbal->query('SELECT * FROM `tb1` WHERE `id` > :id', [':id' => 2]);
        
        self::assertEquals(2, count($result));
        
        self::assertEquals([
            ['id' => 3, 'name' => 'baz'],
            ['id' => 4, 'name' => 'qux'],
        ], iterator_to_array($result));
        
        //
        
        $result = $dbal->query('SELECT * FROM `tb1` WHERE `id` = ?', [2]);
        
        self::assertEquals(1, count($result));
        
        self::assertEquals([
            ['id' => 2, 'name' => 'bar'],
        ], iterator_to_array($result));
        
        //
        
        self::assertTrue($dbal->exec('DROP TABLE `tb1`'));
    }
}
