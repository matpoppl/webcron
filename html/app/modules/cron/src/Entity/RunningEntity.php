<?php

namespace matpoppl\Cron\Entity;

use matpoppl\EntityManager\EntityInterface;
use matpoppl\Cron\AbstractTaskData;

class RunningEntity extends AbstractTaskData implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    /** @var int */
    public $id_task;
    /** @var string */
    public $created;
    /** @var int */
    public $iteration;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
}
