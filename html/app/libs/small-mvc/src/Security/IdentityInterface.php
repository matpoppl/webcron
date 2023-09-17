<?php

namespace matpoppl\SmallMVC\Security;

interface IdentityInterface extends \Serializable
{
    /** @return string[] */
    public function getId();
    /** @return string[] */
    public function getUsername();
    /**
     * 
     * @param NULL|string $key
     * @return array|string|NULL
     */
    public function getExtra(string $key = null);
    
    /** @return string[] */
    public function getRoles();
}
