<?php

namespace matpoppl\Email\Controller;

use matpoppl\Email\Entity\TemplateEntity;
use matpoppl\Email\Entity\TransportEntity;
use matpoppl\Email\Entity\TransportRepository;
use matpoppl\EntityManager\EntityManagerInterface;
use matpoppl\SmallMVC\Controller\AbstractController;

class EmailController extends AbstractController
{
    /** @return EntityManagerInterface */
    protected function getEntityManager()
    {
        return $this->container->get('entity.manager');
    }
    
    /** @return TransportRepository */
    protected function getTransportRepository()
    {
        return $this->getEntityManager()->getRepository(TransportEntity::class);
    }
    
    /** @return TransportRepository */
    protected function getTemplateRepository()
    {
        return $this->getEntityManager()->getRepository(TemplateEntity::class);
    }
    
    public function indexAction()
    {
        return $this->redirect($this->view->route->build('email/templates'));
    }
}
