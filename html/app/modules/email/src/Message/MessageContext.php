<?php

namespace matpoppl\Email\Message;

use matpoppl\Email\Template\Pipeline\Pipeline;
use matpoppl\Email\Template\Pipeline\PipelineRenderer;

class MessageContext
{
    /** @var array */
    private $tplConfig;
    /** @var Pipeline */
    private $pipeline;
    /** @var PipelineRenderer */
    private $renderer;
    
    public function __construct(array $tplConfig, Pipeline $pipeline, PipelineRenderer $renderer)
    {
        $this->tplConfig = $tplConfig;
        $this->pipeline = $pipeline;
        $this->renderer = $renderer;
    }
    
    public function getConfigVar($key)
    {
        return $this->tplConfig[$key] ?? null;
    }
    
    public function render(array $tplVars)
    {
        return $this->pipeline->render($this->renderer, $tplVars);
    }
}
