<?php

use App\Entity;
use App\Controller;
use App\Security;
use App\Translate as AppTranslate;
use matpoppl\Form;
use matpoppl\ServiceManager\Factory\InvokableFactory;
use matpoppl\SmallMVC\View\Helper;
use matpoppl\SmallMVC\Router;
use matpoppl\SmallMVC\Message as MVCMessage;
use matpoppl\SmallMVC\Security as MVCSecurity;
use matpoppl\EntityManager;
use matpoppl\DBAL;
use matpoppl\QueryBuilder;
use matpoppl\Hydrator;
use matpoppl\HttpSession;
use matpoppl\InputFilter;
use matpoppl\DataValidator\ValidatorBuilder;
use matpoppl\DataFilter\FilterBuilder;
use matpoppl\ServiceManager\Factory\InvokableServiceManagerFactory;
use matpoppl\Intl;
use matpoppl\SmallMVC\Utils;
use matpoppl\SecurityAcl;
use matpoppl\HttpMessage\ServerRequestGlobalsFactory;
use matpoppl\Cron\Entity as CronEntity;
use matpoppl\Cron\TaskController;
use matpoppl\PathManager;
use matpoppl\SmallMVC\Controller\ControllerFactory;
use matpoppl\HttpCronTask\SimpleHttpType;
use matpoppl\Mailer;

$appDir = dirname(__DIR__) . '/';

return [
    'dispatcher' => [
        'namespace' => Controller::class,
    ],
    
    'modules' => [
        matpoppl\DataValidator\DataValidatorModule::class,
        matpoppl\ImageCaptcha\ImageCaptchaModule::class,
        matpoppl\Email\EmailModule::class,
    ],
    
    'router' => [
        'routes' => [
            'error/error' => [
                'path' => '/error/error',
                'defaults' => [
                    'controller' => 'error',
                    'action' => 'error',
                ],
            ],
            'tasks' => [
                'path' => '/tasks',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'index',
                ],
            ],
            'task/run' => [
                'path' => '/task/run/{id}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'run',
                ],
                'constraints' => [
                    'id' => '\d{1,10}',
                ],
            ],
            'task/add' => [
                'path' => '/task/add/{type}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'add',
                    'type' => null,
                ],
                'constraints' => [
                    'type' => '[a-z-]*',
                ],
            ],
            'task/edit' => [
                'path' => '/task/{id}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'edit',
                    'id' => 0,
                ],
                'constraints' => [
                    'id' => '\d{1,10}',
                ],
            ],
            'task/del' => [
                'path' => '/task/del',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'delete',
                ],
            ],
            'task/triggers' => [
                'path' => '/task/triggers/{task_id}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'triggers',
                ],
                'constraints' => [
                    'task_id' => '\d{1,10}',
                ],
            ],
            'task/trigger' => [
                'path' => '/task/trigger/{task_id}/{id}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'trigger',
                ],
                'constraints' => [
                    'task_id' => '\d{1,10}',
                    'id' => '\d{1,10}',
                ],
            ],
            'task/trigger/add' => [
                'path' => '/task/trigger/{task_id}',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'trigger',
                ],
                'constraints' => [
                    'task_id' => '\d{1,10}',
                ],
            ],
            'task/trigger/del' => [
                'path' => '/task/trigger/del',
                'defaults' => [
                    'controller' => 'cron.task-controller',
                    'action' => 'deleteTrigger',
                ],
            ],
            'translations' => [
                'path' => '/translations',
                'defaults' => [
                    'controller' => 'translate',
                    'action' => 'index',
                ],
            ],
            'translation/edit' => [
                'path' => '/translation/{id}',
                'defaults' => [
                    'controller' => 'translate',
                    'action' => 'edit',
                    'id' => 0,
                ],
                'constraints' => [
                    'id' => '\d{1,10}',
                ],
            ],
            'users' => [
                'path' => '/users',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'index',
                ],
            ],
            'user/add' => [
                'path' => '/user/add',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'edit',
                    'id' => 0,
                ],
            ],
            'user/edit' => [
                'path' => '/user/{id}',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'edit',
                ],
                'constraints' => [
                    'id' => '\d{1,10}',
                ],
            ],
            'users/del' => [
                'path' => '/user/del',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'delete',
                ],
            ],
            'user/profile' => [
                'path' => '/profile',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'profile',
                ],
            ],
            'user/signout' => [
                'path' => '/signout',
                'defaults' => [
                    'controller' => 'user',
                    'action' => 'signout',
                ],
            ],
            'guest/signin' => [
                'path' => '/signin',
                'defaults' => [
                    'controller' => 'guest',
                    'action' => 'signin',
                ],
            ],
            'guest/reset' => [
                'path' => '/guest/reset',
                'defaults' => [
                    'controller' => 'guest',
                    'action' => 'passwordReset',
                ],
            ],
            'captcha' => [
                'path' => '/captcha/{f}',
                'defaults' => [
                    'controller' => 'image_captcha.controller',
                    'action' => 'index',
                ],
                'constraints' => [
                    'f' => '[a-z]{1}',
                ],
            ],
            'configuration' => [
                'path' => '/configuration',
                'defaults' => [
                    'controller' => 'configuration',
                    'action' => 'index',
                ],
            ],
            'configuration/' => [
                'path' => '/configuration/{action}',
                'defaults' => [
                    'controller' => 'configuration',
                    'action' => 'index',
                ],
                'constraints' => [
                    'action' => '[a-z]{1,50}',
                ],
            ],
            'home' => [
                'path' => '/',
                'defaults' => [
                    'controller' => 'index',
                    'action' => 'index',
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
                [
                    'path' => 'home',
                    'label' => 'Home',
                ],
                'tasks' => [
                    'path' => 'tasks',
                    'label' => 'Tasks',
                    'options' => [
                        'expand' => 'on-path',
                    ],
                    'items' => [
                        'add' => [
                            'label' => 'Add task',
                            'path' => 'task/add',
                        ]
                    ],
                ],
                
                'translations' => [
                    'path' => 'translations',
                    'label' => 'Translations',
                    'item' => ['options' => ['hidden' => true]],
                ],
                
                'configuration' => [
                    'path' => 'configuration',
                    'label' => 'Configuration',
                    'items' => [
                        [
                            'label' => 'PHP Info',
                            'path' => 'configuration/',
                            'pathParams' => ['action'=>'phpinfo'],
                        ], [
                            'label' => 'Database',
                            'path' => 'configuration/',
                            'pathParams' => ['action'=>'dbal'],
                        ],
                    ],
                ],
                'users' => [
                    'path' => 'users',
                    'label' => 'Users',
                    'options' => [
                        'expand' => 'on-path',
                    ],
                    'items' => [
                        'add' => [
                            'label' => 'Add user',
                            'path' => 'user/edit',
                            'pathParams' => ['id'=>0],
                        ]
                    ],
                ],
            ]
        ],
        'user' => [
            'link' => [
                'uri' => '/',
                'label' => 'Home',
            ],
            'items' => [
                [
                    'path' => 'user/profile',
                    'label' => 'profile',
                ],[
                    'path' => 'user/signout',
                    'label' => 'signout',
                ],
            ]
        ],
    ],
    
    'path_manager' => [
        'locations' => [
            'root' => realpath(__DIR__ . '/../..'),
            'modules' => 'root:app/modules',
            'views' => 'root:app/views',
        ],
    ],
    
    'identity_manager' => [
        'safe_routes' => [
            'guest' => 'guest/signin',
            'user' => 'home',
            'moderator' => 'home',
            'admin' => 'home',
        ],
    ],
    
    'security_acl' => [
        'roles' => [
            'guest',
            'user',
            'moderator' => ['user'],
            'admin' => ['moderator'],
        ],
        'resources' => [
            '/',
            '/configuration',
            '/configuration/phpinfo',
            '/guest',
            '/image_captcha.controller',
        ],
        'allow' => [
            ['/', 'user', ['read', 'write']],
            ['/configuration', 'moderator', ['read', 'write']],
            ['/configuration/phpinfo', 'admin', ['read', 'write']],
            ['/guest', 'guest', ['read', 'write']],
            ['/image_captcha.controller', 'guest', ['read']],
        ],
    ],
    
    'dbal' => [
        'dbPrefix' => 'my0_',
        
        'default' => 'sqlite',
        
        'drivers' => [
            'sqlite' => [
                'type' => DBAL\SQLite\SQLiteDriver::class,
                'options' => [
                    'dsn' => $appDir . 'var/sqlite/main.db',
                ],
            ],
        ],
        
        'logger' => 'logger.debug',
    ],
    
    'locale' => [
        'locale' => 'pl_PL',
    ],
    
    'entity_manager' => [
        Entity\UserEntity::class => [
            'hydrator' => [
                'type' => 'ClassMethod',
                'options' => [
                    'getterNamingStrategy' => 'CamelCase2Underscore',
                    'setterNamingStrategy' => 'Underscore2CamelCase',
                ],
            ],
            'className' => Entity\UserEntity::class,
            'repository' => Entity\UserRepository::class,
            'tableName' => 'users',
            'seqCol' => 'id',
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'username' => 'username',
                'roles' => 'roles',
            ],
        ],
        Entity\UserTokenEntity::class => [
            'hydrator' => [
                'type' => 'ClassMethod',
                'options' => [
                    'getterNamingStrategy' => 'CamelCase2Underscore',
                    'setterNamingStrategy' => 'Underscore2CamelCase',
                ],
            ],
            'className' => Entity\UserTokenEntity::class,
            'repository' => Entity\UserTokenRepository::class,
            'tableName' => 'user_tokens',
            'seqCol' => 'id',
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'id_user' => 'id_user',
                'modified' => 'modified',
                'type' => 'type',
                'token' => 'token',
            ],
        ],
        Entity\TranslationEntity::class => [
            'hydrator' => Hydrator\ObjectPropertyHydrator::class,
            'className' => Entity\TranslationEntity::class,
            'repository' => Entity\TranslationRepository::class,
            'tableName' => 'translations',
            'pk' => ['locale', 'domain', 'msgid'],
            'columns' => [
                'locale' => 'locale',
                'domain' => 'domain',
                'msgid' => 'msgid',
                'value' => 'value',
            ],
        ],
        
        CronEntity\TaskEntity::class => [
            'hydrator' => Hydrator\ObjectPropertyHydrator::class,
            'className' => CronEntity\TaskEntity::class,
            'repository' => CronEntity\TaskRepository::class,
            'tableName' => 'cron_tasks',
            'seqCol' => 'id',
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'type' => 'type',
                'name' => 'name',
                'params' => 'params',
            ],
        ],
        CronEntity\TaskTriggerEntity::class => [
            'hydrator' => Hydrator\ObjectPropertyHydrator::class,
            'className' => CronEntity\TaskTriggerEntity::class,
            'repository' => CronEntity\TaskTriggerRepository::class,
            'tableName' => 'cron_task_triggers',
            'seqCol' => 'id',
            'pk' => ['id'],
            'columns' => [
                'id' => 'id',
                'id_task' => 'id_task',
                'active' => 'active',
                'from' => 'from',
                'to' => 'to',
                'next' => 'next',
                'weekdays' => 'weekdays',
                'repeat_type' => 'repeat_type',
                'repeat_every' => 'repeat_every',
            ],
        ],
        CronEntity\RunningEntity::class => [
            'hydrator' => Hydrator\ObjectPropertyHydrator::class,
            'className' => CronEntity\RunningEntity::class,
            'repository' => CronEntity\RunningRepository::class,
            'tableName' => 'cron_running',
            'seqCol' => null,
            'pk' => ['id_task'],
            'columns' => [
                'id_task' => 'id_task',
                'created' => 'created',
                'iteration' => 'iteration',
                'params' => 'params',
            ],
        ],
    ],
    
    /** @see Helper\AssetsHelper */
    'view_assets' => [
        'libraries' => [
            'pQuery' => [
                'js' => [
                    'pQuery.js' => [],
                ],
            ],
            'listing-helpers' => [
                'js' => [
                    'listing-helpers.js' => [],
                ],
                'dependencies' => [
                    'pQuery',
                ],
            ],
            'task/run' => [
                /*
                'css' => [
                    'task/run.css' => [],
                ],
                */
                'js' => [
                    'task-run.js' => [],
                ],
                'dependencies' => [
                    'pQuery',
                ],
            ],
            'phpinfo' => [
                'css' => [
                    'phpinfo.css' => [],
                ],
            ],
        ],
    ],
    
    'image_captcha' => [
        'length' => 6,
        'width' => 400,
        'height' => 90,
    ],
    
    'mailer' => [
        'transport' => [
            'type' => 'Smtp',
            'options' => [
                'host' => 'localhost',
                'port' => '25',
            ],
        ],
        
        'templates' => [
            'guest/password-reset' => [
                'name' => 'Account password recovery',
                'example_vars' => [
                    '{RESET_URL}' => 'http://example.org/password-reset',
                ],
            ],
        ],
    ],
    
    
    'service_manager' => [
        'aliases' => [
            'request' => MVCMessage\Request::class,
            'router' => Router\Router::class,
            'form.builder' => Form\FormBuilder::class,
            'view.helper.route' => Helper\RouteHelper::class,
            'view.helper.meta' => Helper\MetaHelper::class,
            'view.helper.escape' => Helper\EscapeHelper::class,
            'view.helper.t' => 'view.helper.translate',
            'view.helper.translate' => Helper\TranslateHelper::class,
            'view.helper.flashMessenger' => Helper\FlashMessengerHelper::class,
            'view.helper.nav' => Helper\NavHelper::class,
            'view.helper.block' => Helper\BlockHelper::class,
            'view.helper.assets' => Helper\AssetsHelper::class,
            
            'dbal' => DBAL\DBALManager::class,
            'dbal.bulk-tool' => DBAL\BulkTool::class,
            'entity.manager' => EntityManager\EntityManager::class,
            'query.builder' => QueryBuilder\QueryBuilder::class,
            
            'session.manager' => HttpSession\SessionManager::class,
            
            'input.filter.builder' => InputFilter\InputFilterBuilder::class,
            'data.filter.builder' => FilterBuilder::class,
            'data.validator.builder' => ValidatorBuilder::class,
            
            'translator' => Utils\PassThroughTranslator::class,
            'translator()' => AppTranslate\InvokeableTranslator::class,
            
            'path.manager' => PathManager\PathManager::class,
            
            'locale' => Intl\Locale::class,
            
            'csrf.manager' => Security\CsrfManager::class,
            'mvc.acl' => SecurityAcl\AclManager::class,
            'identity.manager' => Security\IdentityManager::class,
            'auth.manager' => Security\AuthManager::class,
            'security.middleware' => MVCSecurity\Middleware::class,
            
            'cron.task-controller' => TaskController::class,
            'cron.task.type.simple-http' => SimpleHttpType::class,
            
            'mailer' => Mailer\Mailer::class,
        ],
        'factories' => [
            MVCMessage\Request::class => ServerRequestGlobalsFactory::class,
            Router\Router::class => Router\RouterFactory::class,
            Form\FormBuilder::class => Form\FormBuilderFactory::class,
            
            Helper\RouteHelper::class => Helper\HelperFactory::class,
            Helper\MetaHelper::class => Helper\HelperFactory::class,
            Helper\EscapeHelper::class => Helper\HelperFactory::class,
            Helper\TranslateHelper::class => Helper\HelperFactory::class,
            Helper\FlashMessengerHelper::class => Helper\HelperFactory::class,
            Helper\NavHelper::class => Helper\HelperFactory::class,
            Helper\BlockHelper::class => Helper\HelperFactory::class,
            Helper\AssetsHelper::class => Helper\HelperFactory::class,
            
            EntityManager\EntityManager::class => EntityManager\EntityManagerFactory::class,
            DBAL\DBALManager::class => DBAL\DBALManagerFactory::class,
            QueryBuilder\QueryBuilder::class => QueryBuilder\QueryBuilderFactory::class,
            DBAL\BulkTool::class => DBAL\BulkToolFactory::class,
            
            CronEntity\TaskRepository::class => EntityManager\Repository\RepositoryFactory::class,
            CronEntity\TaskTriggerRepository::class => EntityManager\Repository\RepositoryFactory::class,
            CronEntity\RunningRepository::class => EntityManager\Repository\RepositoryFactory::class,
            
            Entity\TranslationRepository::class => EntityManager\Repository\RepositoryFactory::class,
            Entity\UserRepository::class => EntityManager\Repository\RepositoryFactory::class,
            Entity\UserTokenRepository::class => EntityManager\Repository\RepositoryFactory::class,
            
            HttpSession\SessionManager::class => HttpSession\SessionManagerFactory::class,
            
            InputFilter\InputFilterBuilder::class => InputFilter\InputFilterBuilderFactory::class,
            FilterBuilder::class => InvokableServiceManagerFactory::class,
            ValidatorBuilder::class => InvokableServiceManagerFactory::class,
            
            Utils\PassThroughTranslator::class => InvokableFactory::class,
            AppTranslate\InvokeableTranslator::class => AppTranslate\InvokeableTranslatorFactory::class,
            
            Intl\Locale::class => Intl\LocaleFactory::class,
            
            PathManager\PathManager::class => PathManager\PathManagerFactory::class,
            
            Security\CsrfManager::class => Security\CsrfManagerFactory::class,
            
            MVCSecurity\Middleware::class => MVCSecurity\MiddlewareFactory::class,
            SecurityAcl\AclManager::class => SecurityAcl\AclManagerFactory::class,
            Security\IdentityManager::class => Security\IdentityManagerFactory::class,
            Security\AuthManager::class => Security\AuthManagerFactory::class,
            
            TaskController::class => ControllerFactory::class,
            SimpleHttpType::class => InvokableFactory::class,
            
            Mailer\Mailer::class => Mailer\MailerFactory::class,
        ],
    ],
    
    'session_manager' => [
        'config' => [
            'name' => 'webcron',
            'save_path' => $appDir . 'var/session',
        ],
    ],
    
    // @TODO cors append headers
    'cors' => [
        'nonce' => true, // , Content-Security-Policy: script-src 'nonce-2726c7f26c' <script nonce="2726c7f26c">
        'headers' => [
            'Content-Security-Policy' => "default-src 'self'",
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'X-XSS-Protection' => '1; mode=block',
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
        ],
    ],
];
