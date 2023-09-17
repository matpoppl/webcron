<?php

use matpoppl\DBAL\SQLite\SQLiteDriver;

require __DIR__ . '/../../../vendor/autoload.php';

$db = new SQLiteDriver([
    'dsn' => ':memory:',
]);

$db->exec('CREATE TABLE `tb1` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT, `name` TEXT )');

$db->exec('INSERT INTO `tb1` (`name`) VALUES (\'foo\'),(\'bar\'),(\'baz\'),(\'qux\')');

$stmt = $db->query('SELECT * FROM `tb1` WHERE `id` > ?', [2]);

var_dump( iterator_to_array($stmt) );
