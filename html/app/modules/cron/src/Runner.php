<?php

namespace matpoppl\Cron;

use Psr\Container\ContainerInterface;
use matpoppl\Cron\Entity\TaskEntity;
use matpoppl\Cron\Entity\TaskTriggerEntity;
use matpoppl\Cron\Entity\TaskRepository;
use matpoppl\Cron\Entity\TaskTriggerRepository;
use matpoppl\Cron\Entity\RunningEntity;
use matpoppl\Cron\Entity\RunningRepository;

class Runner
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function run()
    {
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');
        
        /** @var TaskTriggerRepository $triggerRepository */
        $triggerRepository = $em->getRepository(TaskEntity::class);
        /** @var TaskRepository $taskRepository */
        $taskRepository = $em->getRepository(TaskTriggerEntity::class);
        /** @var RunningRepository $runningRepository */
        $runningRepository = $em->getRepository(RunningEntity::class);
        
        /** @var RunningEntity[] $running */
        $running = [];
        foreach ($runningRepository->fetchRows([]) as $r) {
            $running[$r->task_id] = $r;
        }
        
        $rountTo = 5 * 60;
        $nowTs = time();
        $now = date(DATE_ISO8601, $nowTs - ($nowTs % $rountTo));
        
        foreach ($triggerRepository->fetchNextBefore($now) as $trigger) {
            $taskEntity = $taskRepository->find($trigger->taskId);
            
            $task = $taskFactory($this->container, $taskEntity);
            
            $stepData = '';
            
            $stepData = $task->run($stepData);
        }
    }
}
