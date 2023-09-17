<?php

namespace matpoppl\Email\Controller;

use matpoppl\Email\Form\TemplateModel;
use matpoppl\Email\Entity\TemplateEntity;
use matpoppl\HttpMessage\InMemoryStream;
use matpoppl\Email\Form\SendModel;

class TemplateController extends EmailController
{
    public function indexAction()
    {
        return $this->render('modules:email/views/index.phtml', [
            'templates' => $this->getTemplateRepository()->fetchRows([]),
        ]);
    }
    
    public function editAction()
    {
        $repo = $this->getTemplateRepository();
        
        if ($id = (int) $this->request->get('id')) {
            $entity = $repo->find($id);
            
            if (! $entity) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
            
        } else {
            $entity = new TemplateEntity();
        }
        
        $model = new TemplateModel();
        
        $form = $model->buildEditForm($this->container->get('form.builder'));
        
        if ('POST' === $this->request->getMethod()) {
            
            $data = $this->request->getParsedBody();
            
            $input = $model->buildInputFilter($this->container->get('input.filter.builder'));
            
            $input->setValue($data);
            
            if ($input->isValid($data)) {
                
                $repo->getEntitySpecs()->getHydrator()->hydrate($input->entity->getValue(), $entity);
                
                if (! $repo->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }
                
                $this->view->flashMessenger->add('success', 'Saved');
                return $this->redirect( $this->view->route('email/template', ['id' => (int) $entity->id ]) );
            }
            
            $translator = $this->container->get('translator');
            $form->setValue($input->getValue());
            $form->setErrors($input->getTranslatedMessages($translator));
        } else {
            $formDefaults = $repo->getEntitySpecs()->getHydrator()->extract($entity);

            $form->setValue(['entity' => $formDefaults]);
        }
        
        $form->getAttributes()->merge([
            'method' => 'post',
            'action' => $this->view->route('email/template', ['id' => 0]),
        ]);
        
        $this->view->meta->title('Email template');
        
        $mainMenu = $this->view->nav->getMenu('main')->get('email')->get('templates');
        
        $mainMenu->add([
            'uri' => $this->view->route('email/template', ['id' => (int) $entity->id]),
            'label' => $entity->isNewEntity() ? $this->view->translate('add') : $entity->name,
            'item' => ['options' => ['hidden' => true]],
        ]);
        
        return $this->render('modules:email/views/index.phtml', [
            'form' => $form->getView(),
        ]);
    }
    
    public function renderAction()
    {
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $id = (int) $this->request->get('id');
        
        /** @var TemplateEntity $entity */
        $entity = $em->find(TemplateEntity::class, 'entity', $id);
        
        if (!$entity) {
            throw new \UnexpectedValueException('Entity dont exists');
        }
        
        /** @var \matpoppl\Email\Mailer\MailerManager $mailerMgr */
        $mailerMgr = $this->container->get('mailer.manager');
        
        $msgCtx = $mailerMgr->getMessageContext($entity->sid);
        
        $tplVars = $msgCtx->getConfigVar('example_vars');
        
        $output = $msgCtx->render($tplVars ?? []);
        
        if ('txt' === $this->request->get('format')) {
            return $this->response
            ->withBody(InMemoryStream::fromString($output->contentTxt))
            ->withHeaders([
                'no-store' => 'no-store',
                'Content-Type' => 'text/plain; charset=UTF-8'
            ]);
        }
        
        return $this->response
        ->withBody(InMemoryStream::fromString($output->contentHtml))
        ->withHeaders([
            'no-store' => 'no-store',
            'Content-Type' => 'text/html; charset=UTF-8'
        ]);
    }
    
    public function sendAction()
    {
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $id = (int) $this->request->get('id');
        
        /** @var TemplateEntity $entity */
        $entity = $em->find(TemplateEntity::class, 'entity', $id);
        
        if (!$entity) {
            throw new \UnexpectedValueException('Entity dont exists');
        }
        
        $repo = $this->getTemplateRepository();
        
        $model = new SendModel();
        
        $form = $model->buildEditForm($this->container->get('form.builder'));
        
        if ('POST' === $this->request->getMethod()) {
            
            $data = $this->request->getParsedBody();
            
            $input = $model->buildInputFilter($this->container->get('input.filter.builder'));
            
            $input->setValue($data);
            
            if ($input->isValid($data)) {
                
                var_dump('@TODO send emial');
                var_dump($data);
                
                die();
            }
            
            $translator = $this->container->get('translator');
            $form->setValue($input->getValue());
            $form->setErrors($input->getTranslatedMessages($translator));
        } else {
            $formDefaults = $repo->getEntitySpecs()->getHydrator()->extract($entity);
            
            if (empty($formDefaults['subject'])) {
                $formDefaults['subject'] = $formDefaults['name'];
            }
            
            $form->setValue(['email' => $formDefaults]);
        }
        
        $form->getAttributes()->merge([
            'method' => 'post',
            'action' => $this->view->route('email/template', ['id' => 0]),
        ]);
        
        return $this->render('modules:email/views/send.phtml', [
            'entity' => $entity,
            'form' => $form->getView(),
        ]);
    }
}
