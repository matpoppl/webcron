<?php

namespace matpoppl\SmallMVC;

use Psr\Container\ContainerInterface;
use matpoppl\SmallMVC\Router\MatchResult;
use matpoppl\SmallMVC\Message\RequestInterface;
use matpoppl\SmallMVC\Module\ModuleInterface;
use matpoppl\SmallMVC\Utils\Debugger;

class Application
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $container->set('app', $this);
        
        Debugger::getInstance()->debug(__METHOD__);
        
        $this->loadModules();
    }
        
    public function loadModules()
    {
        Debugger::getInstance()->debug(__METHOD__);
        
        $container = $this->getContainer();
        $cfg = $container->get('config');
        
        foreach ($cfg['modules'] as $moduleName) {
            
            if ($container->has($moduleName)) {
                $module = $container->get($moduleName);
            } else if (class_exists($moduleName)) {
                $module = new $moduleName($container);
                $container->set($moduleName, $module);
            } else {
                throw new \UnexpectedValueException('Unsupported module type `'.$module.'`');
            }
            
            if ($module instanceof ModuleInterface) {
                $module->init($this);
                $cfg->merge($module->getConfig());
            }
        }
        
        $container->configure($cfg['service_manager']);
    }
    
    /** @return ContainerInterface */
    public function getContainer()
    {
        return $this->container;
    }
    
    public function run(RequestInterface $request = null)
    {
        Debugger::getInstance()->debug(__METHOD__);
        
        //$em = $this->container->get('event.manager');
        //$em->trigger('application.run');
        
        if (null === $request) {
            $request = $this->container->get('request');
        }
        
        Debugger::getInstance()->debug(__METHOD__ . ".router");
        
        $router = $this->container->get('router');
        $match = $router->hasMatch($request);

        if (null === $match) {
            $match = new MatchResult([
                'controller' => 'error',
                'action' => 'error',
                'exception' => new Router\MatchException($request, 'Page not found', 404),
            ]);
        }
        
        Debugger::getInstance()->debug(__METHOD__ . ".security");
        
        $response = $this->container->get('security.middleware')->handle($match, $request);
        
        if (! $response) {
            Debugger::getInstance()->debug(__METHOD__ . ".dispatch");
            
            $dispatcher = new Dispatcher( $this->container, $this->container->get('config')['dispatcher'] );
            $response = $dispatcher->dispatch($match, $request);
        }
        
        //$em->trigger('application.close');
        $this->container->get('session.manager')->close();
        
        $response->send();
    }
}
