<?php

namespace App\Security;

use matpoppl\EntityManager\EntityManagerInterface;
use App\Entity\UserTokenEntity;

class AuthRepository
{
    /** @var EntityManagerInterface */
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function findByUsername(string $username)
    {
/*
        $sql = 'SELECT u.*, t.token AS `password_hash` FROM `{users}` AS u
 JOIN `{user_tokens}` AS t ON (t.`id_user`=u.`id` AND t.`type`="password")
 WHERE u.`username`=?';
 */
        /** @var \App\Entity\UserTokenRepository $repo */
        $repo = $this->em->getRepository(UserTokenEntity::class);
        return $repo->findAuthRecord($username);
    }
    
    public function updatePassword($userId, $password)
    {
        /** @var UserTokenEntity $tokenEntity */
        $tokenEntity = $this->em->getRepository(UserTokenEntity::class)->findByUserId($userId);
        
        if (! $tokenEntity) {
            $tokenEntity = new UserTokenEntity();
            $tokenEntity->setIdUser($userId)->setType('password');
        }
        
        $tokenEntity->setToken($password);
        
        if (! $this->em->save($tokenEntity)) {
            throw new \RuntimeException('Password token update failed');
        }
    }
}
