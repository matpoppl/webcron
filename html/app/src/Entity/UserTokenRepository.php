<?php

namespace App\Entity;

use matpoppl\EntityManager\Repository\AbstractRepository;

class UserTokenRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @return UserTokenEntity
     */
    public function findByUserId($id)
    {
        return $this->fetchRow([
            'id_user=?' => $id,
        ]);
    }
    
    /**
     * @param int $id
     * @return UserTokenEntity
     */
    public function findAuthRecord(string $username)
    {
        $query = $this->qb->select()
        ->from(['u' => '{users}'], ['id', 'username', 'roles'])
        ->join(['ut' => '{user_tokens}'], 'u.id=ut.id_user AND ut.type="password"', ['password_hash' => 'token'])
        ->where([
            'u.username=?' => $username,
        ]);
        
        return $this->setFetchMode(self::FETCH_ARRAY)->fetchRow($query);
    }
}
