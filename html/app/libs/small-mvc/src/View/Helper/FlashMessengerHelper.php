<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;
use matpoppl\HttpSession\SessionNamespaceInterface;

class FlashMessengerHelper extends AbstractHelper
{
    /** @var SessionNamespaceInterface */
    private $session;
    
    public function __construct(SessionNamespaceInterface $session)
    {
        $this->session = $session;
    }
    
    public function __invoke($type = null, $message = null)
    {
        return (null === $type) ? $this : $this->add($type, $message);
    }
    
    public function add($type, $message)
    {
        $msgs = $this->session->has($type) ? $this->session->get($type) : [];
        $msgs[] = $message;
        $this->session->set($type, $msgs);
        return $this;
    }
    
    public function getTypes()
    {
        return $this->session->getKeys();
    }
    
    public function getMessages($type)
    {
        if (! $this->session->has($type)) {
            return [];
        }
        
        $ret = $this->session->get($type);
        $this->session->remove($type);
        
        return $ret;
    }
    
    public static function create(ContainerInterface $container, ...$args)
    {
        return new static($container->get('session.manager')->get(__CLASS__));
    }
}
