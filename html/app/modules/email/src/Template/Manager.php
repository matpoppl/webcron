<?php

namespace matpoppl\Email\Template;

class Manager
{
    /** @var array */
    private $templates;
    
    public function __construct(array $options)
    {
        $this->templates = $options['templates'] ?? [];
    }
    
    /**
     *
     * @return string[] { sid: name }
     */
    public function getDefinitionsOptionsList()
    {
        $ret = [];
        
        foreach ($this->templates as $sid => $tpl) {
            $ret[$sid] = "{$tpl['name']} [{$sid}]";
        }
        
        return $ret;
    }

    public function getConfig($sid)
    {
        $cfg = $this->templates[$sid] ?? null;
        
        if (! $cfg) {
            throw new \UnexpectedValueException("Template `{$sid}` config not found");
        }
        
        return $cfg;
    }
}
