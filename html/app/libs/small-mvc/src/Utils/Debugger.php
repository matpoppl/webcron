<?php

namespace matpoppl\SmallMVC\Utils;

use Psr\Log\LoggerInterface;

class Debugger
{
    private $errorMask = 0, $hasErrors = false;
    private $initMemory = null, $initTimestamp = null;
    /** @var LoggerInterface|NULL */
    private $logger;
    /** @var callable|NULL */
    private $prevHanderError;
    private $prevHanderException;
    private $logs = [];
    
    private function __construct()
    {
        $this->log(__METHOD__.'() '.PHP_VERSION);
    }
    
    public function setInit($initTimestamp = null, $initMemory = null)
    {
        $this->initTimestamp = $initTimestamp ?: microtime(true);
        $this->initMemory = $initMemory ?: memory_get_usage();
        return $this;
    }
    
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
    
    public function register($errorMask)
    {
        $this->errorMask = $errorMask;
        $this->prevHanderError = set_error_handler([$this, 'handleError'], $this->errorMask);
        $this->prevHanderException = set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        
        return $this;
    }
    
    public function handleError($type, $msg, $file, $line, $ctx = null)
    {
        $typeName = self::errorTypeName($type);
        
        $this->log("{$typeName}:{$type}# {$msg}\n{$file}:{$line}");
        if (is_callable($this->prevHanderError)) {
            call_user_func($this->prevHanderError, $msg, $file, $line, $ctx);
        }
        return false;
    }
    
    /**
     * 
     * @param \Exception $ex
     */
    public function handleException($ex)
    {
        $exceptions = [];
        do {
            $exceptions[] = $ex;
        } while ($ex = $ex->getPrevious());

        foreach (array_reverse($exceptions) as $ex) {
            $this->critical("{$ex->getCode()}# {$ex->getMessage()}\n{$ex->getFile()}:{$ex->getLine()}\n{$ex->getTraceAsString()}");
        }
        
        if (is_callable($this->prevHanderException)) {
            call_user_func($this->prevHanderException, $ex);
        }
        
        return true;
    }
    
    public function handleShutdown()
    {
        $this->log(__METHOD__.'()');
        
        $this->handleLastError();
        
        if ($this->hasErrors) {
            $this->printLogs();
        }
    }
    
    public function handleLastError()
    {
        $err = error_get_last();
        
        if (is_array($err) && $this->errorMask & $err['type']) {
            $this->handleError($err['type'], $err['message'], $err['file'], $err['line']);
        }
    }
    
    public function log($msg)
    {
        $this->logs[] = [microtime(true), memory_get_usage(), $msg];
        return $this;
    }
    
    public function error($msg)
    {
        $this->hasErrors = true;
        $this->log("[ERROR] {$msg}");
        return $this;
    }
    
    public function critical($msg)
    {
        $this->hasErrors = true;
        $this->log("[CRITICAL] {$msg}");
        return $this;
    }
    
    public function debug($msg)
    {
        assert($this->_debug($msg));
        return $this;
    }
    
    private function _debug($msg)
    {
        $this->log("[DEBUG] {$msg}");
        return true;
    }
    
    public function dumpLogs()
    {
        $firstTime = $prevTime = ($this->initTimestamp ?: 0);
        $firstMem = $prevMem = ($this->initMemory ?: 0);
        
        $msg = '';
        
        foreach ($this->logs as $log) {
            $msg .= sprintf("%.4fs %.4fs % 6dK % 6dK %s\n",
                $log[0] - $prevTime,
                $log[0] - $firstTime,
                ($log[1] - $firstMem) / 1024,
                ($log[1] - $prevMem) / 1024,
                str_replace("\n", "\n\t\t\t\t", $log[2])
                );
            
            $prevTime = $log[0];
            $firstMem = $log[1];
        }
        
        return $msg;
    }
    
    public function printLogs()
    {
        $msg = $this->dumpLogs();
        
        if ($this->logger) {
            $this->logger->debug(date('Y-m-d H:i:s') ."\n". $msg);
            $this->logger->write();
            return;
        }
        
        echo '<pre style="background:#333;color:#888;font:400 14px/1.2 monospace;border:1px solid red;text-align:left;">';
        echo $this->dumpLogs();
        echo '</pre>';
    }
    
    public static function trace()
    {
        $ret = '';
        
        foreach (debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS) as $i => $trace) {
            $ret .= $i . '# '
                . (array_key_exists('class', $trace) ? $trace['class'] : '')
                . (array_key_exists('type', $trace) ? $trace['type'] : '')
                . (array_key_exists('function', $trace) ? $trace['function'].'()' : '')
                . (array_key_exists('file', $trace) ? "\n\t".$trace['file'] : '')
                . (array_key_exists('line', $trace) ? ':'.$trace['line'] : '')
                . "\n";
        }
        
        return $ret;
    }
    
    /**
     * 
     * @param int $type
     * @return string
     */
    public static function errorTypeName($type)
    {
        static $types = null;
        
        if (null === $types) {
            $types = [];
            foreach (get_defined_constants() as $key => $val) {
                if ('E_' === substr($key, 0, 2)) {
                    $types[$val] = $key;
                }
            }
        }
        
        return $types[$type] ?? 'UNKNOWN';
    }
    
    /** @return \matpoppl\SmallMVC\Utils\Debugger */
    public static function &getInstance()
    {
        static $instance = null;
        
        if (null === $instance) {
            $instance = new self();
        }
        
        return $instance;
    }
}
