<?php

namespace matpoppl\Email\Controller;

use matpoppl\Email\Entity\TransportEntity;
use matpoppl\Email\TransportModel;

class TransportController extends EmailController
{
    public function indexAction()
    {
        $rows = $this->getTransportRepository()->setFetchMode('array')->fetchRows([]);
        
        var_dump($rows);
        
        return $this->render('modules:email/views/index.phtml', [
            //'form' => $form->getView(),
        ]);
    }
    
    public function addAction()
    {
        $driver = $this->request->get('driver');
        
        if (! $driver) {
            $drivers = [
                [
                    'sid' => 'mail',
                    'title' => 'mail()',
                    'desc' => 'PHP.mail()',
                ], [
                    'sid' => 'smtp',
                    'title' => 'SMTP',
                    'desc' => 'Simple Mail Transfer Protocol',
                ],
            ];
            
            $this->view->meta->title('Email transport, select driver');
            
            $mainMenu = $this->view->nav->getMenu('main')->get('email')->get('transports');
            
            $mainMenu->add([
                'uri' => $this->view->route('email/transport/add'),
                'label' => $this->view->translate('select driver'),
                'item' => ['options' => ['hidden' => true]],
            ]);
            
            return $this->render('modules:email/views/transport/add.phtml', [
                'drivers' => $drivers,
            ]);
        }
        
        return $this->editAction();
        
    }
    
    public function editAction()
    {
        $routeName = 'email/transport';
        $formDefaults = [];
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $id = (int) $this->request->get('id');
        
        if ($id > 0) {
            $entity = $em->find(TransportEntity::class, 'entity', $id);
            
            if (!$entity) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
            
        } else {
            $driver = $this->request->get('driver');
            
            if (! $driver) {
                // type required
                return $this->redirect($this->view->route('email/transport/add'));
            }
            
            $entity = new TransportEntity();
        }
        
        $model = new TransportModel();
        
        $form = $model->buildForm($this->container->get('form.builder'), [
            'method' => 'post',
            'action' => $this->view->route($routeName, ['id' => (int) $entity->id]),
        ], $formDefaults);
        
        if ('POST' === $this->request->getMethod()) {
            $post = $this->request->getParsedBody();
            
            $input = $model->buildInputFilter($this->container->get('input.filter.builder'));
            
            $input->setValue($post);
            
            if ($input->isValid($post)) {
                $em->getEntitySpecs($entity)->getHydrator()->hydrate($input->getValue(), $entity);
                
                if (!$em->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }
                
                $this->view->flashMessenger->add('success', 'Saved');
                return $this->redirect($this->view->route($routeName, ['id' => (int) $entity->id]));
            }
            
            $form->setMessagesOf('error', $input->getTranslatedMessages($this->container->get('translator')));
            $form->setValue($input->getValue());
        }
        
        $this->view->meta->title('E-mail transport');
        
        return $this->render('modules:email/views/edit.phtml', [
            'form' => $form->getView(),
        ]);
    }
}
