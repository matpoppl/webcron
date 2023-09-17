<?php

namespace matpoppl\Email\Template\Pipeline;

use matpoppl\Email\Entity\TemplateRepository;
use matpoppl\Email\Entity\TemplateEntity;

class PipelineManager
{
    /** @var TemplateRepository */
    private $repo;
    
    public function __construct(TemplateRepository $repo)
    {
        $this->repo = $repo;
    }
    
    /**
     *
     * @param $sid
     * @return Pipeline
     */
    public function get($sid)
    {
        $ret = [];
        
        $ids = [];
        
        $entity = $this->repo->fetchRow(['sid=?' => $sid]);
        
        if (! ($entity instanceof TemplateEntity)) {
            throw new \UnexpectedValueException('TemplateEntity required');
        }
        
        $ids[$entity->id] = $entity->id;
        $ret[] = $entity;
        
        while ($entity->parent) {
            
            $entity = $this->repo->find($entity->parent);
            
            if (! ($entity instanceof TemplateEntity)) {
                throw new \UnexpectedValueException('TemplateEntity required');
            }
            
            if (array_key_exists($sid, $ids)) {
                throw new \UnexpectedValueException('TemplateEntity required');
            }
            
            $ids[$entity->id] = $entity->id;
            $ret[] = $entity;
        }
        
        return new Pipeline($ret);
    }
}
