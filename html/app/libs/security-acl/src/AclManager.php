<?php

namespace matpoppl\SecurityAcl;

class AclManager implements AclInterface
{
    private $resources = [];
    private $roles = [];
    
    private $privileges = [];
    
    private $default = false;
    
    public function addResource(string $resource, $parents = null)
    {
        $this->resources[$resource] = (null === $parents) ? [] : (array) $parents;
        
        foreach ($this->resources[$resource] as $parent) {
            if (! array_key_exists($parent, $this->resources)) {
                throw new \DomainException('Resource dont exists `'.$resource.'`');
            }
        }
        
        return $this;
    }
    
    public function addRole(string $role, $parents = null)
    {
        $this->roles[$role] = (null === $parents) ? [] : (array) $parents;
        
        foreach ($this->roles[$role] as $parent) {
            if (! array_key_exists($parent, $this->roles)) {
                throw new \DomainException('Role dont exists `'.$parent.'`');
            }
        }
        
        return $this;
    }
    
    public function allow(string $resource, string $role, $actions)
    {
        if (! array_key_exists($resource, $this->resources)) {
            throw new \DomainException('Resource dont exists `'.$resource.'`');
        }
        
        if (! array_key_exists($role, $this->roles)) {
            throw new \DomainException('Role dont exists `'.$role.'`');
        }
        
        foreach ((array) $actions as $action) {
            $this->privileges[$role . ':' . $resource . ':' . $action] = true;
        }
        
        return $this;
    }
    
    public function deny(string $resource, string $role, $actions)
    {
        if (! array_key_exists($resource, $this->resources)) {
            throw new \DomainException('Resource dont exists `'.$resource.'`');
        }
        
        if (! array_key_exists($role, $this->roles)) {
            throw new \DomainException('Role dont exists `'.$resource.'`');
        }
        
        foreach ((array) $actions as $action) {
            $this->privileges[$role . ':' . $resource . ':' . $action] = false;
        }
        return $this;
    }
    
    public function check(string $resource, string $role, string $action)
    {
        /* ???? CHECK FOR ROLES/RESOURCES
        if (! array_key_exists($role, $this->roles) || ! array_key_exists($resource, $this->resources)) {
            return false;
        }
        */
        
        
        if (! array_key_exists($resource, $this->resources)) {
            throw new \DomainException('Resource dont exists `'.$resource.'`');
        }
        
        if (! array_key_exists($role, $this->roles)) {
            throw new \DomainException('Role dont exists `'.$role.'`');
        }
        
        
        $original = $role . ':' . $resource . ':' . $action;
        
        if (array_key_exists($original, $this->privileges)) {
            return $this->privileges[$original];
        }
        
        $tmp = $this->findPrivilege($resource, $role, $action);
        
        return (null === $tmp) ? $this->default : $tmp['priv'];
    }
    
    private function findPrivilege(string $resource, string $role, string $action)
    {
        $key = $role . ':' . $resource . ':' . $action;

        if (array_key_exists($key, $this->privileges)) {
            return $this->privileges[$key];
        }
        
        foreach ($this->roles[$role] as $role) {
            $tmp = $this->findPrivilege($resource, $role, $action);
            
            if (null !== $tmp) return [
                'priv' => $tmp,
                $resource, $role, $action
            ];
        }
        
        foreach ($this->resources[$resource] as $resource) {
            $tmp = $this->findPrivilege($resource, $role, $action);
            
            if (null !== $tmp) return [
                'priv' => $tmp,
                $resource, $role, $action
            ];
            
            foreach ($this->roles[$role] as $role) {
                $tmp = $this->findPrivilege($resource, $role, $action);
                
                if (null !== $tmp) return [
                    'priv' => $tmp,
                    $resource, $role, $action
                ];
            }
        }
        
        return null;
    }
    
    public function hasResource(string $resource)
    {
        return array_key_exists($resource, $this->resources);
    }

    public function hasRole(string $role)
    {
        return array_key_exists($role, $this->roles);
    }
    
    public function getRoles()
    {
        return $this->roles;
    }
}
