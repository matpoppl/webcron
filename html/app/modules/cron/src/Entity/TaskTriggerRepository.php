<?php

namespace matpoppl\Cron\Entity;

use matpoppl\EntityManager\Repository\AbstractRepository;

class TaskTriggerRepository extends AbstractRepository
{
    /**
     * 
     * @param int $taskId
     * @return TaskTriggerEntity[]
     */
    public function fetchByTask($taskId)
    {
        return $this->fetchRows([
            'id_task=?' => (int) $taskId,
        ]);
    }
    
    /**
     *
     * @param string|\DateTime $date
     * @return TaskTriggerEntity[]
     */
    public function fetchNextBefore($date)
    {
        if ($date instanceof \DateTime) {
            $date = $date->format(DATE_ISO8601);
        }
        
        return $this->fetchRows([
            'next<=?' => $date,
        ]);
    }
}
