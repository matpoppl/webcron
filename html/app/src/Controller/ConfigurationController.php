<?php
namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;

class ConfigurationController extends AbstractController
{
    public function indexAction()
    {
        $this->view->meta->title('Configuration');
        
        return $this->render('configuration/index.phtml');
    }
    
    public function phpinfoAction()
    {
        $this->view->meta->title('PHP '.PHP_VERSION.' - phpinfo()');
        
        return $this->render('configuration/phpinfo.phtml');
    }
    
    public function dbalAction()
    {
        $this->view->meta->title('Databse abstraction layer');
        
        /** @var \matpoppl\DBAL\DBALManager $dbal */
        $dbal = $this->container->get('dbal');
        
        return $this->render('configuration/dbal.phtml', [
            'dbal' => $dbal,
        ]);
    }
}
