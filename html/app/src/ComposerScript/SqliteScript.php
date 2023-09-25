<?php

namespace App\ComposerScript;

use Composer\Script\Event;
use App\Application;
use matpoppl\DBAL\SQLite\SQLiteDriver;
use matpoppl\DBAL\DBALInterface;

class SqliteScript
{
    /** @var Application */
    private $app;
    
    private function __construct(Event $evt)
    {
        $appRoot = dirname($evt->getComposer()->getConfig()->getConfigSource()->getName());
        $this->app = Application::create(require $appRoot . '/configs/app.php');
    }
    
    private function getDBAL() : DBALInterface
    {
        return $this->app->getContainer()->get('dbal');
    }
    
    private function getDBALDriver() : SQLiteDriver
    {
        return $this->getDBAL()->getDriverBy('sqlite');
    }
    
    private function createDatabase(SQLiteDriver $driver, string $dir)
    {
        $dbPrefix = $this->getDBAL()->getOption('dbPrefix');
        
        foreach (glob(rtrim($dir, '\\/') . '/*.sql') as $file) {
            
            $contents = file_get_contents($file);
            $contents = str_replace('{DBPREFIX}', $dbPrefix, $contents);
            
            foreach (preg_split('#\s?;\s?#', $contents) as $sql) {
                assert($driver->exec($sql), '[SQL ERROR]: ' . $sql);
            }
        }
    }
    
    public static function install(Event $evt)
    {
        $self = new static($evt);
		
        $appRoot = dirname($evt->getComposer()->getConfig()->getConfigSource()->getName());
        $self->createDatabase($self->getDBALDriver(), $appRoot . '/../../data/sqlite');
        
        $tables = $self->getDBALDriver()->listTableNames();
        
        assert(count($tables) > 0, 'Db create failed');
        
        foreach ($tables as $tableName) {
            $sql = $self->getDBALDriver()->describeTable($tableName);
            echo "\t" . str_replace("\n", "\n\t", $sql) . "\n";
        }
    }
}
