<?php

namespace matpoppl\SmallMVC\View\Helper;

use Psr\Container\ContainerInterface;

class BlockHelper extends AbstractHelper
{
    /** @var BufferBlock[] */
    private $blocks = [];
    
    public function __invoke($id)
    {
        return $this->start($id);
    }
    
    public function start($id)
    {
        if (! array_key_exists($id, $this->blocks)) {
            $this->blocks[$id] = new BufferBlock();
        }
        $this->blocks[$id]->start();
        return $this->blocks[$id];
    }
    
    public function end($id)
    {
        return $this->blocks[$id]->end();
    }
}

class BufferBlock
{
    private $contents = null;
    private $started = 0;
    
    public function start()
    {
        $this->started++;
        
        $ok = ob_start();
        
        if (! $ok) {
            throw new \RuntimeException('Buffer start failed');
        }
    }
    
    public function end()
    {
        $this->started++;
        
        if (null === $this->contents) {
            $this->contents = ob_get_contents();
        }
        
        if (! ob_end_clean()) {
            throw new \RuntimeException('Buffer end failed');
        }
        
        return $this->contents;
    }
    
    public function getContents()
    {
        if ($this->started) {
            throw new \RuntimeException('Buffer still running');
        }
        
        return $this->content;
    }
}
