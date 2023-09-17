<?php

namespace matpoppl\Mailer;

use matpoppl\Mailer\Encoder\QuotedPrintableEncoder;

class Headers
{
    /** @var string[][] */
    private $data = [];
    /** @var string[] */
    private $names = [];
    /** @var string[][] */
    private $emails = [];
    
    public function __construct($headers = null)
    {
        if (null !== $headers) {
            $this->setHeaders($headers);
        }
    }
    
    /** @return string[][] */
    public function getHeaders()
    {
        return array_combine($this->names, $this->data);
    }
    
    public function setHeaders(array $headers)
    {
        $this->data = [];
        foreach (array_keys($headers) as $key) {
            $this->set($key, $headers[$key]);
        }
        return $this;
    }
    
    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function has(string $name)
    {
        $key = strtolower($name);
        return array_key_exists($key, $this->data);
    }
    
    public function get(string $name, $default = null)
    {
        $key = strtolower($name);
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
    
    public function getPrefix(string $needle, string $name, $default = null)
    {
        $val = $this->get($name, $default);
        $pos = strpos($val, $needle);
        return $pos > 0 ? substr($val, 0, $pos) : $val;
    }
    
    public function getLine(string $name)
    {
        $key = strtolower($name);
        return array_key_exists($key, $this->data) ? implode(', ', $this->data[$key]) : '';
    }
    
    /**
     *
     * @param string $name
     * @param string|string[] $val
     * @return \matpoppl\Mailer\Headers
     */
    public function set(string $name, $val)
    {
        $key = strtolower($name);
        $this->data[$key] = [];
        return $this->add($name, $val);
    }
    
    /**
     * 
     * @param string $name
     * @param string|string[] $val
     * @return \matpoppl\Mailer\Headers
     */
    public function add(string $name, $val)
    {
        $key = strtolower($name);
        $this->names[$key] = $name;
        if (array_key_exists($key, $this->data)) {
            if (is_array($val)) {
                $this->data[$key] = array_merge($this->data[$key], $val);
            } else {
                $this->data[$key][] = $val;
            }
        } else {
            $this->data[$key] = is_array($val) ? $val : [$val];
        }
        return $this;
    }
    
    public function remove(string $name, $default = null)
    {
        $key = strtolower($name);
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
        return $this;
    }
    
    public function setEmail(string $key, $email, string $personName = null)
    {
        $this->emails[$key] = [];
        return $this->remove($key)->addEmail($key, $email, $personName);
    }
    
    public function addEmail(string $key, $email, string $personName = null)
    {
        if (is_array($email)) {
            foreach ($email as $e => $n) {
                $this->addEmail($key, $e, $n ?: $personName);
            }
            return $this;
        }
        
        $this->emails[$key][$email] = $email;
        
        if ($personName) {
            $this->add($key, trim("\"$personName\" <{$email}>"));
        } else {
            $this->add($key, $email);
        }
        
        return $this;
    }
    
    public function getEmail(string $key)
    {
        return $this->emails[$key];
    }
    
    public function __toString()
    {
        $ret = '';
        
        foreach (array_keys($this->data) as $key) {
            $ret .= "{$this->names[$key]}: {$this->getLine($key)}\r\n";
        }
        
        return $ret;
    }
}
