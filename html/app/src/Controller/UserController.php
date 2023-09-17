<?php

namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;
use App\Entity\UserEntity;
use App\Entity\UserTokenEntity;

class UserController extends AbstractController
{
    public function indexAction()
    {
        $this->view->meta->title($this->view->translate('Users Index'));

        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        return $this->render('user/index.phtml', [
            'rows' => $em->fetchRows(UserEntity::class, 'dbal_stmt', []),
            'csrfHash' => $this->container->get('csrf.manager')
                ->getHash()
        ]);
    }
    
    public function editAction()
    {
        $id = (int) $this->request->get('id');
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        if ($id > 0) {
            $entity = $em->find(UserEntity::class, 'entity', $id);
            
            if (! $entity) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
        } else {
            $entity = new UserEntity();
        }
        
        return $this->handleUserEdit($entity);
    }
    
    private function handleUserEdit(UserEntity $entity)
    {
        $t = $this->view->translate->getFunction();
        
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        $formDefaults = [
            'username' => $entity->getUsername(),
            'roles' => $entity->getRolesArray()
        ];

        $roles = array_keys($this->container->get('mvc.acl')->getRoles());
        $roles = array_combine($roles, array_map(function($role) { return "{$role} role"; }, $roles));
        unset($roles['guest']);
        
        /** @var \matpoppl\Form\Form $form */
        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'class' => 'form form--edit',
                'method' => 'post',
                'action' => $this->view->route('user/edit', [
                    'id' => (int) $entity->getId()
                ])
            ],
            'elements' => [
                'username' => [
                    'type' => 'text',
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true
                    ],
                    'options' => [
                        'label' => $t('username', null, 'app'),
                    ]
                ],
                'password1' => [
                    'type' => 'password',
                    'attributes' => [
                        'class' => 'input-text',
                        'pattern' => '.{8,}',
                    ],
                    'options' => [
                        'label' => $t('set password', null, 'app'),
                    ]
                ],
                'password2' => [
                    'type' => 'password',
                    'attributes' => [
                        'class' => 'input-text'
                    ],
                    'options' => [
                        'label' => $t('repeat password', null, 'app'),
                    ]
                ],
                'roles' => [
                    'type' => 'select',
                    'attributes' => [
                        'class' => 'input-select',
                        'required' => true,
                        'multiple' => true
                    ],
                    'options' => [
                        'label' => $t('user roles', null, 'mvc.acl'),
                        'multiOptions' => array_map($this->container->get('translator()')->withDomain('mvc.acl'), $roles),
                    ]
                ],
                'submit' => [
                    'type' => 'button',
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit'
                    ],
                    'options' => [
                        'label' => $t('save', null, 'app'),
                    ]
                ]
            ]
        ], $formDefaults);

        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'username' => [
                    'required' => true,
                    'filters' => [
                        'StringTrim'
                    ],
                    'validators' => [
                        [
                            'type' => 'StringLength',
                            'options' => [
                                'min' => 2,
                                'max' => 255
                            ]
                        ],
                        [
                            'type' => 'Callback',
                            'options' => [
                                'callback' => function ($data) use ($entity, $em) {

                                    if (! $entity->isNewEntity() && $data === $entity->getUsername()) {
                                        return false;
                                    }

                                    $userRepo = $em->getRepository(UserEntity::class);

                                    if (! $userRepo->findByUsername($data)) {
                                        return false;
                                    }
                                    
                                    return [['Username already exists', null, 'app']];
                                }
                            ]
                        ]
                    ]
                ],
                'password1' => [
                    'filters' => [
                        'StringTrim',
                        'ToNull'
                    ],
                    'validators' => [
                        [
                            'type' => 'StringLength',
                            'options' => [
                                'min' => 8,
                                'max' => 512
                            ]
                        ]
                    ]
                ],
                'password2' => [
                    'filters' => [
                        'StringTrim',
                        'ToNull'
                    ],
                    'validators' => [
                        [
                            'type' => 'Match',
                            'options' => [
                                'name' => 'password1'
                            ]
                        ]
                    ]
                ],
                'roles' => [
                    'required' => true,
                    'multiple' => true,
                    'validators' => [
                        [
                            'type' => 'InArray',
                            'options' => [
                                'haystack' => array_keys($roles)
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

        if ('POST' === $this->request->getMethod()) {
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            if ($inputFilter->isValid($post)) {

                $em->getEntitySpecs($entity)
                    ->getHydrator()
                    ->hydrate($inputFilter->getValue(), $entity);

                if (! $em->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }
                
                if ($pw = $inputFilter->get('password2')->getValue()) {
                    
                    $repo = $em->getRepository(UserTokenEntity::class);
                    
                    /** @see \App\Security\AuthManager::updateUserToken() */
                    $ok = $this->container->get('auth.manager')->updateUserToken($repo, $entity->getId(), $pw);
                    
                    if (! $ok) {
                        throw new \UnexpectedValueException('TokenEntity save error');
                    }
                }
                
                $this->view->flashMessenger->add('success', 'Saved');
                return $this->redirect($this->view->route('user/edit', [
                    'id' => $entity->getId()
                ]));
            }

            $translator = $this->container->get('translator');
            $form->setValue($inputFilter->getValue())
                ->setMessagesOf('error', $inputFilter->getTranslatedMessages($translator));
            $this->response = $this->response->withStatus(400);
        }

        $this->view->meta->title('User edit');
        
        if ($entity->getId() > 0) {
            $this->view->nav()->getMenu('main')->get('users')->add([
                'uri' => $this->view->route('user/edit', ['id' => $entity->getId()]),
                'label' => $t('User', null, 'app'),
                'item' => ['options' => ['hidden' => true]],
            ]);
        } else {
            $this->view->nav()->getMenu('main')->get('users')->add([
                'uri' => $this->view->route('user/add'),
                'label' => $t('User add', null, 'app'),
                'item' => ['options' => ['hidden' => true]],
            ]);
        }
        
        return $this->render('user/edit.phtml', [
            'form' => $form->getView()
        ]);
    }
    
    public function profileAction()
    {
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        /** @var \App\Security\IdentityManager $im */
        $im = $this->container->get('identity.manager');
        
        $entity = $em->find(UserEntity::class, 'entity', $im->getIdentity()->getId());
        
        if (! $entity) {
            throw new \UnexpectedValueException('Entity dont exists');
        }
        
        return $this->handleUserEdit($entity);
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
        
        $post = $this->request->getParsedBody()->getArrayCopy();
        
        $inputFilter->setValue($post);
        
        if ($inputFilter->isValid($post)) {
            /** @var \matpoppl\EntityManager\EntityManager $em */
            $em = $this->container->get('entity.manager');
            
            $repo = $em->getRepository(UserEntity::class);
            
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
    
    public function signoutAction()
    {
        $this->container->get('identity.manager')->signout();
        return $this->redirect($this->view->route('guest/signin'));
    }
}
