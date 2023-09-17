<?php

namespace App\Entity;

use matpoppl\EntityManager\EntityInterface;

class UserEntity implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    /** @var int */
    private $id;
    /** @var string */
    private $username;
    /** @var string */
    private $roles;
    
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
    
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    /** @return string */
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    
    /** @return string */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /** @return string[] */
    public function getRolesArray()
    {
        return strlen($this->roles) > 0 ? explode(',', substr($this->roles, 1, -1)) : [];
    }
    
    public function setRoles($roles)
    {
        if (is_array($roles)) {
            $this->roles = ','.implode(',', $roles).',';
        } else {
            $this->roles = $roles;
        }
        
        return $this;
    }
}
