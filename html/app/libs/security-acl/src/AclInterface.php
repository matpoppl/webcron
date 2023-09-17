<?php

namespace matpoppl\SecurityAcl;

interface AclInterface
{
    /**
     *
     * @param string $resource
     * @return bool
     */
    public function hasResource(string $resource);
    
    /**
     * 
     * @param string $resource
     * @param string|string[]|NULL $parents
     * @return AclInterface
     */
    public function addResource(string $resource, $parents = null);
    
    /**
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role);
    
    /**
     * 
     * @param string $role
     * @param string|string[]|NULL $parents
     * @return AclInterface
     */
    public function addRole(string $role, $parents = null);
    
    /**
     * 
     * @param string $resource
     * @param string $role
     * @param string|string[] $actions
     * @return AclInterface
     */
    public function allow(string $resource, string $role, $actions);
    
    /**
     * 
     * @param string $resource
     * @param string $role
     * @param string|string[] $actions
     * @return AclInterface
     */
    public function deny(string $resource, string $role, $actions);

    /**
     * 
     * @param string $resource
     * @param string $role
     * @param string $action
     * @return bool
     */
    public function check(string $resource, string $role, string $action);
}
