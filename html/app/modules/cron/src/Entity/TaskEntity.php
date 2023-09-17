<?php

namespace matpoppl\Cron\Entity;

use matpoppl\EntityManager\EntityInterface;
use matpoppl\Cron\AbstractTaskData;

class TaskEntity extends AbstractTaskData implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    /** @var int */
    public $id;
    /** @var string */
    public $type;
    /** @var string */
    public $name;
    /** @var string */
    public $params;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
    
    /** @return string[] */
    public function getHeadersAsArray()
    {
        $ret = [];
        
        /** @var string $headers */
        $headers = $this->getParam('headers');
        
        if (! is_array($headers)) {
            return $ret;
        }
        
        foreach (explode("\n", $headers) as $line) {
            list($name, $value) = explode(':', $line);
            $ret[trim($name)] = trim($value);
        }
        
        return $ret;
    }
}
