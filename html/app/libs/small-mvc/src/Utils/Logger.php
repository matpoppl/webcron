<?php

namespace matpoppl\SmallMVC\Utils;

use Psr\Log\AbstractLogger;

function interpolate($message, array $context = array())
{
    // build a replacement array with braces around the context keys
    $replace = array();
    foreach ($context as $key => $val) {
        // check that the value can be cast to string
        if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
            $replace['{' . $key . '}'] = $val;
        }
    }
    
    // interpolate replacement values into the message and return
    return strtr($message, $replace);
}

class Logger extends AbstractLogger
{
    /** @var LogWriterInterface */
    private $writer;
    private $logs = [];
    
    public function __construct(LogWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    public function log($level, $message, array $context = array())
    {
        $this->logs[] = "[{$level}] " . interpolate($message, $context);
        return $this;
    }
    
    public function write()
    {
        return $this->writer->write($this->logs);
        
        echo "\n\n".'<br a="" /><pre style="font:400 14px/1.2 monospace;color:#999;background:#222;border:1px solid #f00;text-align:left;padding:0.5em 1em;margin:1px;">';
        echo implode("\n", $this->logs);
        echo "</pre>\n\n";
    }
}
