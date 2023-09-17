<?php

namespace matpoppl\Cron\Entity;

use matpoppl\EntityManager\EntityInterface;

class TaskTriggerEntity implements EntityInterface
{
    const REPEAT_MINUTES = '1';
    const REPEAT_HOURS = '2';
    const REPEAT_DAYS = '3';
    const REPEAT_MONTHS = '4';
    
    /** @var bool */
    private $_isNewRecord = true;

    /** @var int */
    public $id;
    /** @var int */
    public $id_task;
    /** @var int */
    public $active = 1;
    /** @var string */
    public $from;
    /** @var string */
    public $to;
    /** @var string */
    public $next;
    /** @var string */
    public $weekdays;
    /** @var string One of self::REPEAT_* */
    public $repeat_type;
    /** @var string */
    public $repeat_every;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
    
    public function getWeekdays()
    {
        return str_split($this->weekdays, 1);
    }
    
    public function setWeekdays($weekdays)
    {
        if (empty($weekdays)) {
            $this->weekdays = null;
        } else if (is_array($weekdays)) {
            $this->weekdays = implode('', $weekdays);
        } else {
            $this->weekdays = $weekdays;
        }
        
        return $this;
    }
    
    public function setFrom($date)
    {
        if (is_array($date)) {
            $this->from = trim($date['date'] . ' ' . $date['time']) ?: null;
        } else {
            $this->from = $date;
        }
        
        return $this;
    }
    
    public function setTo($date)
    {
        if (is_array($date)) {
            $this->to = trim($date['date'] . ' ' . $date['time']) ?: null;
        } else {
            $this->to = $date;
        }
        
        return $this;
    }
    
    public function calcNextDatetime()
    {
        $now = time();
        $ret = strtotime($this->from);
        $format = $this->getRepeatTimeFormat();
        
        do {
            $ret = strtotime($format, $ret);
        } while ($ret < $now);
        
        $this->next = date(DATE_ISO8601, $ret);
    }
    
    public function getRepeatTimeFormat()
    {
        switch ($this->repeat_type) {
            case self::REPEAT_MINUTES:
                return "+{$this->repeat_every} minutes";
            case self::REPEAT_HOURS:
                return "+{$this->repeat_every} hours";
            case self::REPEAT_DAYS:
                return "+{$this->repeat_every} days";
            case self::REPEAT_MONTHS:
                return "+{$this->repeat_every} months";
        }
        
        throw new \UnexpectedValueException('Unsupported repeat type');
    }
}
