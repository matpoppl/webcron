<?php

namespace matpoppl\Email\Entity;

use matpoppl\EntityManager\EntityInterface;

class TransportEntity implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    public $id = 0;
    public $name;
    public $email;
    public $driver;
    public $hostname;
    public $port;
    public $encrypt;
    public $auth;
    public $username;
    public $password;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
}
