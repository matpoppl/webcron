<?php

namespace matpoppl\SmallMVC\Security;

use App\Security\AuthManagerInterface;

interface IdentityManagerInterface
{
    /** @return IdentityInterface  */
    public function getIdentity();
    
    public function signin(AuthManagerInterface $authMgr, $username, $password);
}
