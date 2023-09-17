<?php

namespace matpoppl\Cron;

use matpoppl\SmallMVC\Controller\AbstractController;

class TaskController extends AbstractController
{
    public function indexAction()
    {
        $this->view->meta->title('Tasks Index');

        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');

        return $this->render('modules:cron/views/index.phtml', [
            'rows' => $em->fetchRows(Entity\TaskEntity::class, 'array', []),
            'csrfHash' => $this->container->get('csrf.manager')->getHash(),
        ]);
    }
    
    public function addAction()
    {
        $type = $this->request->get('type');
        
        $this->view->meta->title('Tasks types');
        
        if (! $type) {
            return $this->render('modules:cron/views/types.phtml', [
                'types' => [
                    'simple-http' => [
                        'title' => 'Simple HTTP Task',
                        'desc' => 'Donec luctus sit amet justo vehicula tristique. Aenean id purus feugiat, molestie odio id, varius ex. Suspendisse tempor ligula ut leo semper rhoncus. Ut turpis velit, placerat eget erat non, elementum fermentum mauris.',
                    ],
                ],
            ]);
        }
        
        return $this->editAction();
    }
    
    public function editAction()
    {
        $id = (int) $this->request->get('id');

        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');

        if ($id > 0) {
            $entity = $em->find(Entity\TaskEntity::class, 'entity', $id);

            if (!$entity) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
            
            $formAction = $this->view->route('task/edit', ['id' => (int) $entity->id]);
        } else {
            $type = $this->request->get('type');
            
            if (! $type) {
                // type required
                return $this->redirect($this->view->route('task/add'));
            }
            
            $entity = new Entity\TaskEntity();
            $entity->type = $type;
            $formAction = $this->view->route('task/add', ['type' => $type]);
        }
        
        /** @see \matpoppl\HttpCronTask\SimpleHttpType */
        $taskType = $this->container->get('cron.task.type.' . $entity->type);
        
        /** @see \matpoppl\InputFilter\InputFilterBuilder::createInputFilter() */
        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'name' => [
                    'filters' => [
                        'StringTrim',
                    ],
                    'validators' => [
                        [
                            'type' => 'StringLength',
                            'options' => ['min' => 2, 'max' => 255],
                        ],
                    ],
                ],
                'params' => [
                    'inputs' => $taskType->getInputFilter(),
                ],
                'csrf' => [
                    'validators' => [
                        [
                            'type' => 'Equals',
                            'options' => ['expected' => $this->container->get('csrf.manager')->getHash()],
                        ],
                    ],
                ],
            ],
        ]);

        $formDefaults = $em->getEntitySpecs($entity)->getHydrator()->extract($entity);
        $formDefaults['params'] = $entity->getParams();
        
        /** @see \matpoppl\Form\FormBuilder::createForm() */
        /** @var \matpoppl\Form\Form $form */
        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'class' => 'form form--edit',
                'method' => 'post',
                'action' => $formAction,
            ],
            'elements' => [
                'name' => [
                    'type' => 'text',
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true,
                    ],
                    'options' => [
                        'label' => 'task name',
                    ],
                ],
                'params' => [
                    'type' => 'fieldset',
                    'elements' => $taskType->getForm(),
                ],
                'submit' => [
                    'type' => 'button',
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit',
                    ],
                    'options' => [
                        'label' => 'save',
                    ],
                ],
            ],
        ], $formDefaults);
        
        if ('POST' === $this->request->getMethod()) {
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            if ($inputFilter->isValid($post)) {
                $em->getEntitySpecs($entity)->getHydrator()->hydrate($inputFilter->getValue(), $entity);
                
                $entity->setParams($inputFilter->get('params')->getValue());
                
                if (!$em->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }

                $this->view->flashMessenger->add('success', 'Saved');
                return $this->redirect($this->view->route('task/edit', ['id' => $entity->id]));
            }

            $translator = $this->container->get('translator');
            $form->setValue($inputFilter->getValue())
            ->setMessagesOf('error', $inputFilter->getTranslatedMessages($translator));
            $this->response = $this->response->withStatus(400);
        }

        $this->view->meta->title('Task edit');

        if ($entity->id > 0) {
            $this->view->nav->getMenu('main')->get('tasks')->add([
                'uri' => $this->view->route('task/edit', ['id' => (int) $entity->id]),
                'label' => 'Task',
                'item' => ['options' => ['hidden' => true]],
            ]);
        } else {
            $this->view->nav->getMenu('main')->get('tasks')->get('add')->add([
                'uri' => $this->view->route('task/add', ['type' => $this->request->get('type')]),
                'label' => $this->request->get('type'),
                'item' => ['options' => ['hidden' => true]],
            ]);
        }
        
        return $this->render('modules:cron/views/edit.phtml', [
            'form' => $form->getView(),
        ]);
    }
    
    public function deleteAction()
    {
        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'id' => [
                    'multiple' => true,
                    'required' => true,
                    'filters' => [
                        'ToInt',
                    ],
                    'validators' => [
                        [
                            'type' => 'NumberBetween',
                            'options' => [
                                'min' => 1,
                                'max' => pow(2, 16),
                            ]
                        ]
                    ]
                ],
                'csrf' => [
                    'required' => true,
                    'validators' => [
                        [
                            'type' => 'Equals',
                            'options' => [
                                'expected' => $this->container->get('csrf.manager')->getHash()
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        $post = $this->request->getParsedBody();
        
        $inputFilter->setValue($post);
        
        if ($inputFilter->isValid($post)) {
            /** @var \matpoppl\EntityManager\EntityManager $em */
            $em = $this->container->get('entity.manager');
            
            $repo = $em->getRepository(Entity\TaskEntity::class);
            
            $deleted = 0;
            
            foreach ($inputFilter->get('id')->getValue() as $id) {
                $entity = $repo->find($id);
                
                if ($entity) {
                    $deleted += $em->remove($entity);
                }
            }
            
            $this->view->flashMessenger->add('success', $this->view->translate('Deleted `{n}` entities', [
                '{n}' => $deleted,
            ], 'app'));
        }
        
        return $this->redirectBack();
    }
    
    public function deleteTriggerAction()
    {
        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'id' => [
                    'multiple' => true,
                    'required' => true,
                    'filters' => [
                        'ToInt',
                    ],
                    'validators' => [
                        [
                            'type' => 'NumberBetween',
                            'options' => [
                                'min' => 1,
                                'max' => pow(2, 16),
                            ]
                        ]
                    ]
                ],
                'csrf' => [
                    'required' => true,
                    'validators' => [
                        [
                            'type' => 'Equals',
                            'options' => [
                                'expected' => $this->container->get('csrf.manager')->getHash()
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        $post = $this->request->getParsedBody();
        
        $inputFilter->setValue($post);
        
        if ($inputFilter->isValid($post)) {
            /** @var \matpoppl\EntityManager\EntityManager $em */
            $em = $this->container->get('entity.manager');
            
            $repo = $em->getRepository(Entity\TaskTriggerEntity::class);
            
            $deleted = 0;
            
            foreach ($inputFilter->get('id')->getValue() as $id) {
                $entity = $repo->find($id);
                
                if ($entity) {
                    $deleted += $em->remove($entity);
                }
            }
            
            $this->view->flashMessenger->add('success', $this->view->translate('Deleted `{n}` entities', [
                '{n}' => $deleted,
            ], 'app'));
        }
        
        return $this->redirectBack();
    }
    
    public function triggersAction()
    {
        $task_id = (int) $this->request->get('task_id');
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $taskEntity = $em->find(Entity\TaskEntity::class, $task_id);
        
        $this->view->meta->title('Task triggers');
        
        $menuTasks = $this->view->nav->getMenu('main')->get('tasks')->add([
            'uri' => $this->view->route('task/edit', ['id' => $task_id]),
            'label' => $taskEntity->name,
            'item' => ['options' => ['hidden' => true]],
        ], 'task');
        
        $menuTasks->get('task')->add([
            'uri' => $this->view->route('task/triggers', ['task_id' => $task_id]),
            'label' => 'Triggers',
            'item' => ['options' => ['hidden' => true]],
        ]);
        
        return $this->render('modules:cron/views/triggers.phtml', [
            'rows' => $em->getRepository(Entity\TaskTriggerEntity::class)->setFetchMode('dbal_stmt')->fetchByTask($task_id),
            'taskEntity' => $taskEntity,
            'csrfHash' => $this->container->get('csrf.manager')->getHash(),
        ]);
    }
    
    public function triggerAction()
    {
        $id = (int) $this->request->get('id');
        $task_id = (int) $this->request->get('task_id');
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        /** @var Entity\TaskEntity $taskEntity */
        $taskEntity = $em->find(Entity\TaskEntity::class, $task_id);
        
        if (! $taskEntity) {
            throw new \UnexpectedValueException('TaskEntity don\'t exists');
        }
        
        /** @var Entity\TaskTriggerEntity $entity */
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        if ($id > 0) {
            /** @var Entity\TaskTriggerEntity $entity */
            $entity = $em->find(Entity\TaskTriggerEntity::class, $id);
            
            if (! ($entity instanceof Entity\TaskTriggerEntity)) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
            
            $formAction = $this->view->route('task/trigger', ['task_id' => (int) $taskEntity->id, 'id' => (int) $entity->id]);
        } else {
            $entity = new Entity\TaskTriggerEntity();
            
            $entity->taskId = (int) $taskEntity->id;
            
            $formAction = $this->view->route('task/trigger/add', ['task_id' => (int) $taskEntity->id]);
        }
        
        $formDefaults = $em->getEntitySpecs($entity)->getHydrator()->extract($entity);
        $formDefaults['weekdays'] = $entity->getWeekdays();
        
        /** @var \matpoppl\Translate\TranslatorInterface $translator */
        $t = $this->container->get('translator()');
        
        /** @see \matpoppl\Form\FormBuilder::createForm() */
        /** @var \matpoppl\Form\Form $form */
        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'class' => 'form form--edit',
                'method' => 'post',
                'action' => $formAction,
            ],
            'elements' => [
                'repeat_type' => [
                    'type' => 'select',
                    'attributes' => [
                        'class' => 'input-select',
                        'required' => true,
                    ],
                    'options' => [
                        'label' => 'repeat type',
                        'multiOptions' => array_map($t->withDomain('matpoppl.cron'), [
                            '' => '-- choose --',
                            Entity\TaskTriggerEntity::REPEAT_MINUTES => 'minutes',
                            Entity\TaskTriggerEntity::REPEAT_HOURS => 'hours',
                            Entity\TaskTriggerEntity::REPEAT_DAYS => 'days',
                            Entity\TaskTriggerEntity::REPEAT_MONTHS => 'months',
                        ]),
                    ],
                ],
                'repeat_every' => [
                    'type' => 'input',
                    'attributes' => [
                        'class' => 'input-input',
                        'type' => 'number',
                        'required' => true,
                        'min' => 1,
                        'step' => 1,
                    ],
                    'options' => [
                        'label' => $t('repeat every', null, 'matpoppl.cron'),
                    ],
                ],
                'from' => [
                    /** @see \matpoppl\Form\Element\DateTimeElement */
                    'type' => 'datetime',
                    'attributes' => [
                        'class' => 'input-datetime',
                        'required' => true,
                        'step' => 60,
                    ],
                    'options' => [
                        'label' => $t('start date time', null, 'matpoppl.cron'),
                    ],
                ],
                'to' => [
                    'type' => 'datetime',
                    'attributes' => [
                        'class' => 'input-datetime',
                        'required' => true,
                        'step' => 60,
                    ],
                    'options' => [
                        'label' => $t('to date time', null, 'matpoppl.cron'),
                    ],
                ],
                'weekdays' => [
                    'type' => 'select',
                    'attributes' => [
                        'class' => 'input-select-multiple',
                        'multiple' => true,
                    ],
                    'options' => [
                        'label' => $t('week days', null, 'matpoppl.cron'),
                        'multiOptions' => array_map($t, [
                            // -- ISO 8601
                            '1' => 'monday',
                            '2' => 'tuesday',
                            '3' => 'wednesday',
                            '4' => 'thursday',
                            '5' => 'friday',
                            '6' => 'saturday',
                            '7' => 'sunday',
                        ]),
                    ],
                ],
                'active' => [
                    'type' => 'checkbox',
                    'attributes' => [
                        'class' => 'input-checkbox',
                    ],
                    'options' => [
                        'label' => 'is active',
                    ],
                ],
                'btnSubmit' => [
                    'type' => 'button',
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit',
                    ],
                    'options' => [
                        'label' => 'save',
                    ],
                ],
            ],
        ], $formDefaults);
        
        /** @see \matpoppl\InputFilter\InputFilterBuilder::createInputFilter() */
        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'from' => [
                    'required' => true,
                    'inputs' => [
                        'date' => [
                            'required' => true,
                            'filters' => [
                                'StringTrim',
                            ],
                            /** @see \matpoppl\DataValidator\StringLengthValidator */
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 10, 'max' => 10],
                                ], [
                                    'type' => 'DateTimeFormat',
                                    'options' => ['format' => 'Y-m-d'],
                                ],
                            ],
                        ],
                        'time' => [
                            'required' => true,
                            'filters' => [
                                'StringTrim',
                            ],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 5, 'max' => 5],
                                ], [
                                    'type' => 'DateTimeFormat',
                                    'options' => ['format' => 'H:i'],
                                ],
                            ],
                        ],
                    ],
                ],
                'to' => [
                    'inputs' => [
                        'date' => [
                            'filters' => [
                                'StringTrim',
                                'ToNull',
                            ],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 10, 'max' => 10],
                                ], [
                                    'type' => 'DateTimeFormat',
                                    'options' => ['format' => 'Y-m-d'],
                                ],
                            ],
                        ],
                        'time' => [
                            'filters' => [
                                'StringTrim',
                                'ToNull',
                            ],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 5, 'max' => 5],
                                ], [
                                    'type' => 'DateTimeFormat',
                                    'options' => ['format' => 'H:i'],
                                ],
                            ],
                        ],
                    ],
                ],
                'repeat_type' => [
                    'validators' => [
                        [
                            'type' => 'InArray',
                            'options' => ['haystack' => [
                                Entity\TaskTriggerEntity::REPEAT_MINUTES,
                                Entity\TaskTriggerEntity::REPEAT_HOURS,
                                Entity\TaskTriggerEntity::REPEAT_DAYS,
                                Entity\TaskTriggerEntity::REPEAT_MONTHS,
                            ]],
                        ],
                    ],
                    
                ],
                'repeat_every' => [
                    'filters' => [
                        'ToInt',
                    ],
                    'validators' => [
                        [
                            'type' => 'NumberBetween',
                            'options' => ['min' => 1, 'max' => 9999],
                        ],
                    ],
                ],
                'weekdays' => [
                    'multiple' => true,
                    'filters' => ['ToNull'],
                    'validators' => [
                        [
                            'type' => 'InArray',
                            'options' => ['haystack' => str_split('1234567', 1)],
                        ],
                    ],
                    
                ],
                'active' => [
                    'filters' => ['ToInt'],
                ],
                'csrf' => [
                    'validators' => [
                        [
                            'type' => 'Equals',
                            'options' => ['expected' => $this->container->get('csrf.manager')->getHash()],
                        ],
                    ],
                ],
            ],
        ]);
        
        if ('POST' === $this->request->getMethod()) {
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            if ($inputFilter->isValid($post)) {

                $em->getEntitySpecs($entity)->getHydrator()->hydrate($inputFilter->getValue(), $entity);
                
                $entity->setFrom($inputFilter->from->getValue());
                $entity->setTo($inputFilter->to->getValue());
                $entity->setWeekdays($inputFilter->weekdays->getValue());
                $entity->calcNextDatetime();
                
                if (!$em->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }
                
                $this->view->flashMessenger->add('success', 'Saved');
                return $this->redirect($this->view->route('task/trigger', ['task_id' => $entity->taskId, 'id' => $entity->id]));
            }
            
            $translator = $this->container->get('translator');
            
            $form->setValue($inputFilter->getValue())
            ->setMessagesOf('error', $inputFilter->getTranslatedMessages($translator));
            $this->response = $this->response->withStatus(400);
        }
        
        $menuTasks = $this->view->nav->getMenu('main')->get('tasks')->add([
            'uri' => $this->view->route('task/edit', ['id' => $task_id]),
            'label' => $taskEntity->name,
            'item' => ['options' => ['hidden' => true]],
        ], 'task');
        
        $menuTriggers = $menuTasks->get('task')->add([
            'uri' => $this->view->route('task/triggers', ['task_id' => $task_id]),
            'label' => 'Triggers',
            'item' => ['options' => ['hidden' => true]],
        ], 'triggers');
        
        if ($entity->isNewEntity()) {
            $this->view->meta->title('New trigger');
            
            $menuTriggers->get('triggers')->add([
                'uri' => $this->view->route('task/trigger/add', ['task_id' => $taskEntity->id]),
                'label' => 'New trigger',
                'item' => ['options' => ['hidden' => true]],
            ]);
        } else {
            $this->view->meta->title('Trigger');
            
            $menuTriggers->get('triggers')->add([
                'uri' => $this->view->route('task/trigger', ['task_id' => $taskEntity->id, 'id' => $entity->id]),
                'label' => 'Trigger',
                'item' => ['options' => ['hidden' => true]],
            ]);
        }
        
        return $this->render('modules:cron/views/trigger.phtml', [
            'form' => $form->getView()->renameElements(),
            'taskEntity' => $taskEntity,
        ]);
    }
    
    public function runAction()
    {
        $taskId = (int) $this->request->get('id');
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $entity = $em->find(Entity\TaskEntity::class, $taskId);
        
        if (! $entity) {
            throw new \Exception('Task not found', 404);
        }
        
        if ('POST' === $this->request->getMethod()) {
            
            $stepData = new StepData();
            // @TODO iterate ?
            $stepData->iteration = 1;
            $stepData->setParams([
                'query' => http_build_query($this->request->getQueryParams()),
                'body' => ''.$this->request->getBody(),
            ]);
            
            $task = new \matpoppl\HttpCronTask\HttpTask($this->container);
            
            return $this->renderJSON($task->run($entity, $stepData));
        }
        
        $tasksMenu = $this->view->nav->getMenu('main')->get('tasks')->add([
            'uri' => $this->view->route('task/edit', ['id' => $taskId]),
            'label' => $entity->name,
            'item' => ['options' => ['hidden' => true]],
        ], 'task');
        
        $tasksMenu->get('task')->add([
            'uri' => $this->view->route('task/run', ['id' => $taskId]),
            'label' => $this->view->translate('Run'),
            'item' => ['options' => ['hidden' => true]],
        ], 'task');
        
        $this->view->meta->title('Run task');
        
        return $this->render('modules:cron/views/run.phtml', [
            'taskEntity' => $entity,
        ]);
    }
}
