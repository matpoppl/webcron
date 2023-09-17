<?php

namespace App\Security;

use matpoppl\HttpSession\SessionNamespaceInterface;
use matpoppl\SmallMVC\Security\IdentityManagerInterface;
use matpoppl\SmallMVC\Security\IdentityInterface;

use const matpoppl\SmallMVC\Security\CODE_OK;

class IdentityManager implements IdentityManagerInterface
{
    /** @var Identity */
    private $identity;
    
    /** @var SessionNamespaceInterface */
    private $session;
    
    /** @var string[] */
    private $safeRoutes = [];
    
    public function __construct(SessionNamespaceInterface $session, array $options = null)
    {
        $this->session = $session;
        
        if (null === $options) {
            return;
        }
        
        $this->safeRoutes = $options['safe_routes'] ?? [];
    }
    
    public function signin(AuthManagerInterface $authMgr, $username, $password)
    {
        $result = $authMgr->signin($username, $password);
        
        if (CODE_OK !== $result->getCode() || ! $result->hasIdentity()) {
            return $result;
        }
        
        $identity = $result->getIdentity();
        
        $this->identity = new Identity($identity['id'], $identity['username'], $identity['roles']);
        
        $this->session->set('identity', serialize($this->identity));
        
        return $result;
    }
    
    public function getIdentity()
    {
        if (null === $this->identity) {
            
            if (! $this->session->has('identity')) {
                $identity = new Identity(0, 'guest', ['guest']);
            } else {
                $identity = unserialize($this->session->get('identity'));
                
                if (! ($identity instanceof IdentityInterface)) {
                    // force signout
                    $this->session->destroy();
                    throw new \UnexpectedValueException('Identity unserialization failed');
                }
            }
            
            $this->identity = $identity;
        }
        
        return $this->identity;
    }
    
    /**
     * 
     * @param string|NULL $role
     * @return string|NULL
     */
    public function getSafeRouteFor($role = null)
    {
        if (null === $role) {
            foreach ($this->getIdentity()->getRoles() as $role) {
                if (array_key_exists($role, $this->safeRoutes)) {
                    return $this->safeRoutes[$role];
                }
            }
            return null;
        }
        
        return array_key_exists($role, $this->safeRoutes) ? $this->safeRoutes[$role] : null;
    }
    
    public function signout()
    {
        $this->session->destroy();
    }
}
