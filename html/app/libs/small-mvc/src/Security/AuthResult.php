<?php

namespace matpoppl\SmallMVC\Security;

class AuthResult implements AuthResultInterface
{
    /** @var int */
    private $code;
    /** @var string */
    private $message;
    private $identity;
    
    public function __construct(int $code, string $message, $identity = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->identity = $identity;
    }
    
    /** @return bool */
    public function hasIdentity()
    {
        return null !== $this->identity;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
    
    /** @return int */
    public function getCode()
    {
        return $this->code;
    }
    
    /** @return string */
    public function getMessage()
    {
        return $this->message;
    }
}
