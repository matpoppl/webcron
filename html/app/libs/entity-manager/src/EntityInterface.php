<?php

namespace matpoppl\EntityManager;

interface EntityInterface
{
    /**
     * 
     * @param NULL|bool $isNewRecord
     * @return bool|EntityInterface
     */
    public function isNewEntity($isNewRecord = null);
}
