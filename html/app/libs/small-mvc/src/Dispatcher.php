<?php

namespace matpoppl\SmallMVC;

use Psr\Container\ContainerInterface;
use matpoppl\SmallMVC\Controller\ControllerInterface;
use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SmallMVC\Message\RequestInterface;

class Dispatcher
{
    /** @var ContainerInterface */
    private $container;
    /** @var array */
    private $options;

    public function __construct(ContainerInterface $container, array $options)
    {
        $this->container = $container;
        $this->options = $options;
    }

    private function getController(string $id)
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        } else if (is_subclass_of($id, ControllerInterface::class)) {
            return new $id($this->container);
        }

        $className = $this->options['namespace'] . '\\' . ucfirst($id) . 'Controller';

        if (! class_exists($className)) {
            return null;
        }
        
        return new $className($this->container);
    }

    public function dispatch(MatchInterface $match, RequestInterface $req)
    {
        $ctrl = $this->getController($match->getController());

        if (! ($ctrl instanceof ControllerInterface)) {
            throw new \InvalidArgumentException('Unsupported controller type `'.$match->getController().'`');
        }

        $ctrl->setContainer($this->container);

        return $ctrl->dispatch($match, $req);
    }
}