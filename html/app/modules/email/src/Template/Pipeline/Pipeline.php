<?php

namespace matpoppl\Email\Template\Pipeline;

use matpoppl\Email\Entity\TemplateEntity;

class Pipeline
{
    /** @var TemplateEntity[] */
    private $entities;
    
    public $contentTxt;
    public $contentHtml;
    
    public $to;
    public $cc;
    public $bcc;
    public $replyTo;
    public $subject;
    
    /**
     * 
     * @param TemplateEntity[] $entities
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
        
        foreach ($entities as $entity) {
            $this->subject = $entity->subject ?: $entity->name;
            break;
        }
        
        /* 
        foreach ($entities as $entity) {
            if (! $this->from) {
                $this->from = $entity->from;
            }
            if (! $this->to) {
                $this->to = $entity->to;
            }
            if (! $this->cc) {
                $this->cc = $entity->cc;
            }
            if (! $this->bcc) {
                $this->bcc = $entity->bcc;
            }
            if (! $this->replyTo) {
                $this->replyTo = $entity->replyTo;
            }
        }
         */
    }
    
    public function render(PipelineRenderer $renderer, array $vars)
    {
        list($this->contentTxt, $this->contentHtml) = $renderer->render($this->entities, $vars);
        
        return $this;
    }
}
