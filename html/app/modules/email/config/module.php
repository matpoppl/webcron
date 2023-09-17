<?php

namespace matpoppl\Email;

use matpoppl\SmallMVC\Controller\ControllerFactory;
use matpoppl\Hydrator\ObjectPropertyHydrator;
use matpoppl\EntityManager\Repository\RepositoryFactory;
use matpoppl\ServiceManager\Factory\InvokableFactory;
use matpoppl\Email\Controller;

return [
    'router' => [
        'routes' => [
            'email' => [
                'path' => '/email',
                'defaults' => [
                    'controller' => 'module.email.ctrl.templates',
                    'action' => 'index',
                ],
            ],
            'email/templates' => [
                'path' => '/email/templates',
                'defaults' => [
                    'controller' => 'module.email.ctrl.templates',
                    'action' => 'index',
                ],
            ],
            'email/template' => [
                'path' => '/email/template/{id}',
                'defaults' => [
                    'controller' => 'module.email.ctrl.templates',
                    'action' => 'edit',
                    'id' => 0,
                ],
                'constraints' => [
                    'id' => '\d{1,6}',
                ],
            ],
            'email/template/render' => [
                'path' => '/email/template/render/{id}',
                'defaults' => [
                    'controller' => 'module.email.ctrl.templates',
                    'action' => 'render',
                    'id' => 0,
                ],
                'constraints' => [
                    'id' => '\d{1,6}',
                ],
            ],
            'email/template/send' => [
                'path' => '/email/template/send/{id}',
                'defaults' => [
                    'controller' => 'module.email.ctrl.templates',
                    'action' => 'send',
                    'id' => 0,
                ],
                'constraints' => [
                    'id' => '\d{1,6}',
                ],
            ],
            'email/transports' => [
                'path' => '/email/transports',
                'defaults' => [
                    'controller' => 'module.email.ctrl.transports',
                    'action' => 'index',
                ],
            ],
            'email/transport/add' => [
                'path' => '/email/transport/add',
                'defaults' => [
                    'controller' => 'module.email.ctrl.transports',
                    'action' => 'add',
                ],
            ],
            'email/transport/add/driver' => [
                'path' => '/email/transport/add/{driver}',
                'defaults' => [
                    'controller' => 'module.email.ctrl.transports',
                    'action' => 'add',
                ],
                'constraints' => [
                    'driver' => '\w{1,100}',
                ],
            ],
            'email/transport' => [
                'path' => '/email/transport/{id}',
                'defaults' => [
                    'controller' => 'module.email.ctrl.transports',
                    'action' => 'edit',
                ],
                'constraints' => [
                    'id' => '\d{1,6}',
                ],
            ],
        ],
    ],
    
    'menus' => [
        'main' => [
            'link' => [
                'uri' => '/',
                'label' => 'Home',
            ],
            'items' => [
                'email' => [
                    'path' => 'email',
                    'label' => 'E-mail',
                    'items' => [
                        'transports' => [
                            'label' => 'e-mail transports',
                            'path' => 'email/transports',
                        ],
                        'templates' => [
                            'label' => 'e-mail templates',
                            'path' => 'email/templates',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'module.email.ctrl.templates' => Controller\TemplateController::class,
            'module.email.ctrl.transports' => Controller\TransportController::class,
            'email.template.manager' => Template\Manager::class,
            'email.template.pipeline.manager' => Template\Pipeline\PipelineManager::class,
            'data.filter.EmailAddressDetailsFilter' => \matpoppl\Email\DataFilter\EmailAddressDetailsFilter::class,
            'mailer.manager' => \matpoppl\Email\Mailer\MailerManager::class,
        ],
        'factories' => [
            Controller\TemplateController::class => ControllerFactory::class,
            Controller\TransportController::class => ControllerFactory::class,
            Entity\TransportRepository::class => RepositoryFactory::class,
            Entity\TemplateRepository::class => RepositoryFactory::class,
            Template\Manager::class => Template\ManagerFactory::class,
            Template\Pipeline\PipelineManager::class => Template\Pipeline\PipelineManagerFactory::class,
            \matpoppl\Email\DataFilter\EmailAddressDetailsFilter::class => InvokableFactory::class,
            \matpoppl\Email\Mailer\MailerManager::class => \matpoppl\Email\Mailer\MailerManagerFactory::class,
        ],
    ],
    
    'entity_manager' => [
        Entity\TransportEntity::class => [
            'hydrator' => ObjectPropertyHydrator::class,
            'className' => Entity\TransportEntity::class,
            'repository' => Entity\TransportRepository::class,
            'tableName' => 'email_transports',
            'seqCol' => 'id',
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
                'driver' => 'driver',
                'hostname' => 'hostname',
                'port' => 'port',
                'encrypt' => 'encrypt',
                'auth' => 'auth',
                'username' => 'username',
                'password' => 'password',
            ],
        ],
        Entity\TemplateEntity::class => [
            'hydrator' => ObjectPropertyHydrator::class,
            'className' => Entity\TemplateEntity::class,
            'repository' => Entity\TemplateRepository::class,
            'tableName' => 'email_templates',
            'seqCol' => false,
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'sid' => 'sid',
                'active' => 'active',
                'name' => 'name',
                'subject' => 'subject',
                'parent' => 'parent',
                'content_txt' => 'contentTxt',
                'content_html' => 'contentHtml',
            ],
        ],
    ],
    
];












