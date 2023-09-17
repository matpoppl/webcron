<?php

namespace App\Security;

class PasswordHasher implements PasswordHasherInterface
{
    private $algo = PASSWORD_BCRYPT;
    private $options = ['cost' => 14];
    
    public function __construct(array $options = null)
    {
        if (null === $options) {
            return;
        }
        
        if (isset($options['algo'])) {
            $this->algo = $options['algo'] ?? PASSWORD_BCRYPT;
            unset($options['algo']);
        }
        
        $this->options = $options;
    }
    
    public function hash($password)
    {
        return password_hash($password, $this->algo, $this->options);
    }
    
    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    public function needsRehash($hash)
    {
        return password_needs_rehash($hash, $this->algo, $this->options);
    }
    
    public function listAlgorithms()
    {
        return password_algos();
    }
}

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    function password_algos(): array
    {
        $algos = [PASSWORD_BCRYPT];
        defined('PASSWORD_ARGON2I')  && $algos[] = PASSWORD_ARGON2I;
        defined('PASSWORD_ARGON2ID') && $algos[] = PASSWORD_ARGON2ID;
        return $algos;
    }
}
