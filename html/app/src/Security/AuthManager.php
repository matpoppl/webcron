<?php

namespace App\Security;

use App\Entity\UserTokenRepository;
use matpoppl\SmallMVC\Security\AuthResult;
use const matpoppl\SmallMVC\Security\CODE_AUTH_FAILED;
use const matpoppl\SmallMVC\Security\CODE_OK;
use const matpoppl\SmallMVC\Security\CODE_USER_DONT_EXISTS;
use App\Entity\UserTokenEntity;

class AuthManager implements AuthManagerInterface
{
    /** @var AuthRepository */
    private $authRepo;
    /** @var PasswordHasherInterface */
    private $hasher;
    
    public function __construct(AuthRepository $authRepo, PasswordHasherInterface $hasher)
    {
        $this->authRepo = $authRepo;
        $this->hasher = $hasher;
    }
    
    public function signin(string $username, string $password)
    {
        $record = $this->authRepo->findByUsername($username);
        
        if (! $record) {
            return new AuthResult(CODE_USER_DONT_EXISTS, 'User dont exists');
        }
        
        if (! $this->hasher->verify($password, $record['password_hash'])) {
            return new AuthResult(CODE_AUTH_FAILED, 'Invalid password');
        }
        
        if ($this->hasher->needsRehash($record['password_hash'])) {
            $this->authRepo->updatePassword($record['id'], $this->hasher->hash($password));
        }
        
        return new AuthResult(CODE_OK, 'Ok', [
            'id' => $record['id'],
            'username' => $record['username'],
            'roles' => explode(',', substr($record['roles'], 1, -1))
        ]);
    }
    
    public function updateUserToken(UserTokenRepository $repo, $userId, $password)
    {
        $tokenEntity = $repo->findByUserId($userId);
        
        if (! $tokenEntity) {
            $tokenEntity = new UserTokenEntity();
            $tokenEntity->setIdUser($userId);
            $tokenEntity->setType('password');
        }
        
        $tokenEntity->setToken($this->hasher->hash($password));
        $tokenEntity->setModified(date('Y-m-d H:i:s'));
        
        return $repo->save($tokenEntity);
    }
}
