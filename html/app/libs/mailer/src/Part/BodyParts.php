<?php

namespace matpoppl\Mailer\Part;

use matpoppl\Mailer\Random;
use matpoppl\Mailer\Headers;

class BodyParts implements \Countable
{
    /** @var PartInterface[] */
    protected $parts = [];
    /** @var string */
    private $boundary = null;
    
    public function __construct()
    {}
    
    public function set(PartInterface $part)
    {
        $this->parts = [$part];
        return $this;
    }
    
    public function add(PartInterface $part)
    {
        $this->parts[] = $part;
        return $this;
    }
    
    public function setBody($body, $contentType, $charset)
    {
        $this->parts = [];
        return $this->addBody($body, $contentType, $charset);
    }
    
    public function addBody($body, $contentType, $charset)
    {
        return $this->add(StreamPart::createFromString($body, sprintf("{$contentType}; charset={$charset}")));
    }
    
    public function getBoundaryName()
    {
        if (null === $this->boundary) {
            $this->boundary = '------------' . Random::createId(12);
        }
        
        return $this->boundary;
    }
    
    public function getHeaders()
    {
        if (count($this->parts) < 2) {
            return null;
        }
        
        $boundary = $this->getBoundaryName();
        $headers = new Headers();
        $headers->set('Content-Type', "multipart/alternative; boundary=\"{$boundary}\"");
        return $headers;
    }
    
    public function count(): int
    {
        return count($this->parts);
    }
    
    public function renderParts()
    {
        $ret = '';
        
        $useBoundry = $this->count() > 1;
        
        $boundary = $this->getBoundaryName();
        
        foreach ($this->parts as $part) {
            $body = wordwrap($part, 998, "\r\n");
            $body = str_replace("\r\n.", "\r\n..", $body);
            
            if ($useBoundry) {
                $ret .= "--{$boundary}\r\n";
            }
            
            $ret .= "{$part->getHeaders()}\r\n{$body}\r\n\r\n";
        }
        
        if ($useBoundry) {
            $ret .= "--{$boundary}--";
        }
        
        return $ret;
    }
    
    public function __toString()
    {
        $ret = '';
        
        if ($headers = $this->getHeaders()) {
            $ret .= "{$headers}\r\n";
        }
        
        return $ret . $this->renderParts();
    }
}
