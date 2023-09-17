<?php

namespace matpoppl\EventManager;

class EventManager
{
    /** @var callable */
    private $listeners = [];
    
    /**
     * 
     * @param string $type
     * @param \Closure|callable $listener
     * @throws \UnexpectedValueException
     * @return self
     */
    public function attach($type, $listener)
    {
        if (is_callable($listener)) {
            $listener = \Closure::fromCallable($listener);
        }
        
        if (! ($listener instanceof \Closure)) {
            throw new \UnexpectedValueException('Unsupported closure type');
        }
        
        if (! array_key_exists($type, $this->listeners)) {
            $this->listeners[$type] = [];
        }
        
        $this->listeners[$type][] = $listener;
        
        return $this;
    }
    
    /**
     * 
     * @param string|EventInterface $evt
     * @param mixed ...$args
     * @throws \UnexpectedValueException
     * @return self
     */
    public function trigger($evt, ...$args)
    {
        if (is_string($evt)) {
            $evt = new Event($evt);
        }
        
        if (! ($evt instanceof EventInterface)) {
            throw new \UnexpectedValueException('Unsupported event type');
        }
        
        $type = $evt->getName();
        
        if (! array_key_exists($type, $this->listeners)) {
            return $this;
        }
        
        foreach ($this->listeners[$type] as $listener) {
            
            $ok = $listener($evt, ...$args);
            
            // stopPropagation
            if (false === $ok) {
                break;
            }
        }
        
        return $this;
    }
}
