<?php

namespace matpoppl\Email\Mailer;

use matpoppl\Email\Template\Manager as TemplateManager;
use matpoppl\Email\Template\Pipeline\PipelineManager;
use matpoppl\Email\Template\Pipeline\PipelineRenderer;
use matpoppl\Email\Message\MessageContext;

class MailerManager
{
    /** @var TemplateManager */
    private $tplManager;
    /** @var PipelineManager */
    private $pipeManager;
    /** @var PipelineRenderer */
    private $renderer;
    
    public function __construct(TemplateManager $tplManager, PipelineManager $pipeManager, PipelineRenderer $renderer)
    {
        $this->tplManager = $tplManager;
        $this->pipeManager = $pipeManager;
        $this->renderer = $renderer;
    }
    
    /**
     * 
     * @param string $messageSID
     * @return \matpoppl\Email\Message\MessageContext
     */
    public function getMessageContext(string $messageSID)
    {
        $tplConfig = $this->tplManager->getConfig($messageSID);
        $pipeline = $this->pipeManager->get($messageSID);
        return new MessageContext($tplConfig, $pipeline, $this->renderer);
    }
}
