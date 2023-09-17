<?php

namespace App\Entity;

use matpoppl\EntityManager\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{
    /**
     * @param string $username
     * @return UserEntity
     */
    public function findByUsername($username)
    {
        return $this->fetchRow([
            'username=?' => $username,
        ]);
    }
}
