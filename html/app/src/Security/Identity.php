<?php

namespace App\Security;

use matpoppl\SmallMVC\Security\IdentityInterface;

class Identity implements IdentityInterface
{
    /** @var string */
    private $id;
    /** @var string */
    private $username;
    /** @var string[] */
    private $roles;
    /** @var array */
    private $extra;
    
    public function __construct($id, $username, $roles, array $extra = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->roles = is_array($roles) ? $roles : [$roles];
        $this->extra = $extra ?: [];
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function getExtra(string $key = null)
    {
        if (null === $key) {
            return $this->extra;
        }
        
        return array_key_exists($key, $this->extra) ? $this->extra[$key] : null;
    }
    
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->roles = $data['roles'] ?? [];
        $this->extra = $data['extra'] ?? [];
    }
    
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'roles' => $this->roles,
            'extra' => $this->extra,
        ];
    }
    
    public function serialize(): ?string
    {
        return serialize($this->__serialize());
    }

    public function unserialize($serialized) : void
    {
        $data = unserialize($serialized);
        $this->__serialize($data);
    }
}
