<?php

namespace matpoppl\Email;

use matpoppl\SmallMVC\Module\ModuleInterface;
use matpoppl\SmallMVC\Application;
use matpoppl\SmallMVC\Utils\Debugger;

class EmailModule implements ModuleInterface
{
    public function init(Application $app)
    {
        //$this->install($app);
    }
    
    public function getConfig() : array
    {
        return require __DIR__ . '/../config/module.php';
    }
    
    public function install(Application $app)
    {
        Debugger::getInstance()->debug(__METHOD__);
        $app->getContainer()->get('dbal.bulk-tool')->execFromGlob(__DIR__ . '/../sql/*.sql');
    }
}
