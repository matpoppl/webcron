<?php

namespace matpoppl\SmallMVC\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use matpoppl\HttpMessage\InMemoryStream;
use matpoppl\SmallMVC\Message\RequestInterface;
use matpoppl\SmallMVC\Message\Response;
use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SmallMVC\View;
use matpoppl\SmallMVC\View\ViewDataFactory;

abstract class AbstractController implements ControllerInterface
{
    /** @var ContainerInterface */
    protected $container;
    /** @var RequestInterface */
    protected $request;
    /** @var Response */
    protected $response;
    /** @var \matpoppl\SmallMVC\View\ViewData */
    protected $view = null;

    abstract public function indexAction();

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    public function dispatch(MatchInterface $match, RequestInterface $request)
    {
        // init view
        $this->getViewData();
        
        $this->request = $request;
        $this->response = new Response();
/*
        // @TODO move ACL c to middleware
        if ($acl = $this->container->get('mvc.acl')) {
            $check = new AccessCheck($acl);
            $identity = $this->container->get('identity.manager')->getIdentity();
            
            if (! $check->check($match, $request, $identity)) {
                $this->view->flashMessenger->add('danger', $this->view->translate('Roles `{roles}` do not have access to `{route}`', [
                    '{roles}' => implode(',', $identity->getRoles()),
                    '{route}' => $match->getParam('_route_name'),
                ], 'acl'));
                
                $safeRoute = $this->container->get('identity.manager')->getSafeRouteFor();
                
                if (null === $safeRoute) {
                    throw new \UnexpectedValueException('Role without safe route');
                }
                
                if ($safeRoute === $match->getParam('_route_name')) {
                    throw new \UnexpectedValueException('Safe route infinite loop detected');
                }
                
                return $this->redirect($this->view->route($safeRoute));
            }
        }
*/
        $action = $match->getAction() . 'Action';

        $request->setParams($match->getParams());

        if (! method_exists($this, $action)) {
            throw new \UnexpectedValueException('Action `'.$action.'` dont exists');
        }
        
        $response = $this->{$action}();
        
        if ($response instanceof ResponseInterface) {
            $this->response = $response;
        }

        return $this->response;
    }

    /** @return View\ViewData */
    public function getViewData()
    {
        if (null === $this->view) {
            $factory = new ViewDataFactory();
            $this->view = $factory($this->container, View\ViewData::class, realpath(__DIR__ . '/../../../../views/'));
            //$this->view->addTemplateDir(realpath(__DIR__ . '/../../../../views/'));
        }
        return $this->view;
    }
    
    public function render($template, array $viewData = null)
    {
        $view = $this->getViewData();
        
        if (null !== $viewData) {
            $view->addData($viewData);
        }
        
        $body = InMemoryStream::fromString($view->render($template));
        $this->response = $this->response
        ->withBody($body)
        ->withHeaders([
            'no-store' => 'no-store',
            //'Content-Length' => $body->getSize(),
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
        
        return $this->response;
    }
    
    public function renderJSON($data)
    {
        $json = json_encode($data);
        
        $body = InMemoryStream::fromString($json);
        $this->response = $this->response
        ->withBody($body)
        ->withHeaders([
            'no-store' => 'no-store',
            //'Content-Length' => $body->getSize(),
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
        
        return $this->response;
    }
    
    public function redirect($url, $code = null)
    {
        $this->response = new Response();
        
        $this->response = $this->response->withHeader('Location', $url)
        ->withStatus( $code ?: 302 );
        
        return $this->response;
    }
    
    public function redirectBack($code = null)
    {
        $url = $this->request->getHeaderLine('Referer');
        
        $uri = $this->request->getUri();
        
        $baseUrl = $uri->getScheme() . '://' . $uri->getAuthority() . '/';
        
        if (0 !== strpos($url, $baseUrl)) {
            throw new \UnexpectedValueException('Invalid Referer');
        }
        
        return $this->redirect($url, $code);
    }
}
