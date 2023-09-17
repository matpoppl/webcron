<?php

namespace App\Entity;

use matpoppl\EntityManager\EntityInterface;

class UserTokenEntity implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    /** @var int */
    private $id;
    /** @var int */
    private $id_user;
    /** @var string */
    private $modified;
    /** @var string */
    private $type;
    /** @var string */
    private $token;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
    
    /** @return int */
    public function getId()
    {
        return $this->id;
    }
    
    /** @return int */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /** @return string */
    public function getModified()
    {
        return $this->modified;
    }
    
    /** @return string */
    public function getType()
    {
        return $this->type;
    }
    
    /** @return string */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param number $id
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param number $user_id
     * @return static
     */
    public function setIdUser($user_id)
    {
        $this->id_user = $user_id;
        return $this;
    }

    /**
     * @param string $modified
     * @return static
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * @param string $type
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $token
     * @return static
     */
    public function setToken($token)
    {
        $this->token = $token;
        $this->setModified(date(DATE_ISO8601));
        return $this;
    }
}
