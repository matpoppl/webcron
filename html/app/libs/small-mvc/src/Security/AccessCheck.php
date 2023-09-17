<?php

namespace matpoppl\SmallMVC\Security;

use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SecurityAcl\AclInterface;
use Psr\Http\Message\RequestInterface;

class AccessCheck
{
    /** @var AclInterface */
    private $acl;
    
    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }
    
    public function check(MatchInterface $match, RequestInterface $request, IdentityInterface $identity)
    {
        $ctrl = $match->getController();
        $act = $match->getAction();
        
        switch (strtoupper($request->getMethod())) {
            case 'GET':
            case 'OPTIONS':
                $action = 'read';
                break;
            default:
                $action = 'write';
                break;
        }
        
        foreach([
            '/' . $ctrl . '/' . $act,
            '/' . $ctrl,
            '/',
        ] as $resource) {

            if (! $this->acl->hasResource($resource)) continue;

            foreach ($identity->getRoles() as $role) {
                if (! $this->acl->hasRole($role)) continue;

                if ($this->acl->check($resource, $role, $action)) {
                    return true;
                }
            }
        }

        return false;
    }
}
