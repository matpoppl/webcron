<?php

namespace App\Security;

use matpoppl\HttpSession\SessionNamespaceInterface;

class CsrfManager
{
    /** @var SessionNamespaceInterface */
    private $session;

    public function __construct(SessionNamespaceInterface $session)
    {
        $this->session = $session;
        $this->random = new RandomSource();
    }
    
    /** @return string */
    public function getHash() : string
    {
        $hash = $this->session['hash'];
        if (empty($hash)) {
            mt_srand();
            $this->session['hash'] = $hash = hash('sha512', $this->random->getBytes(1024));
        }
        return $hash;
    }
    
    public function isCurrentHash($userHash) : bool
    {
        return hash_equals($this->getHash(), $userHash);
    }
}
