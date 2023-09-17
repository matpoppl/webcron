<?php

namespace App\Entity;

use matpoppl\EntityManager\EntityInterface;

class TranslationEntity implements EntityInterface
{
    private $_isNewRecord = true;

    public $locale;
    public $domain = 'default';
    public $msgid;
    public $value;

    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }

        $this->_isNewRecord = $isNewRecord;

        return $this;
    }
}
