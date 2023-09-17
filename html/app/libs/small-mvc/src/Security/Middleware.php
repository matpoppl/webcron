<?php

namespace matpoppl\SmallMVC\Security;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use matpoppl\SmallMVC\Router\MatchInterface;
use App\Security\IdentityManager;
use matpoppl\SmallMVC\Router\Router;
use matpoppl\SmallMVC\Message\Response;

class Middleware
{
    /** @var AccessCheck */
    private $accessCheck;
    /** @var IdentityManager */
    private $identityManager;
    /** @var Router */
    private $router;
    
    public function __construct(AccessCheck $accessCheck, IdentityManager $identityManager, Router $router)
    {
        $this->accessCheck = $accessCheck;
        $this->identityManager = $identityManager;
        $this->router = $router;
    }
    
    public function handle(MatchInterface $match, ServerRequestInterface $request): ?ResponseInterface
    {
        $identity = $this->identityManager->getIdentity();
        
        if ($this->accessCheck->check($match, $request, $identity)) {
            return null;
        }
        
        $safeRoute = $this->identityManager->getSafeRouteFor();
        
        if (null === $safeRoute) {
            throw new \UnexpectedValueException('Role without safe route');
        }
        
        if ($safeRoute === $match->getParam('_route_name')) {
            throw new \UnexpectedValueException('Safe route infinite loop detected');
        }
        
        $res = new Response();
        return $res->withHeader('Location', $this->router->get($safeRoute)->buildPath())
        ->withStatus( 302 );
    }
}
