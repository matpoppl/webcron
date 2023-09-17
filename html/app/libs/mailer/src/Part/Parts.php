<?php

namespace matpoppl\Mailer\Part;

use matpoppl\Mailer\Headers;

class Parts extends BodyParts
{
    /** @var BodyParts */
    private $bodyParts;
    
    public function __construct()
    {
        $this->bodyParts = new BodyParts();
    }
    
    public function setBody($body, $contentType, $charset)
    {
        $this->bodyParts->setBody($body, $contentType, $charset);
        return $this;
    }
    
    public function addBody($body, $contentType, $charset)
    {
        $this->bodyParts->addBody($body, $contentType, $charset);
        return $this;
    }
    
    public function addPart(PartInterface $part)
    {
        $this->parts[] = $part;
        return $this;
    }
    
    public function getHeaders()
    {
        if (count($this->parts) < 1) {
            return $this->bodyParts->getHeaders();
        }
        
        $boundary = $this->getBoundaryName();
        $headers = new Headers();
        $headers->set('Content-Type', "multipart/mixed; boundary=\"{$boundary}\"");
        return $headers;
    }
    
    public function count(): int
    {
        return $this->bodyParts->count() + parent::count();
    }
    
    public function __toString()
    {
        if (count($this->parts) < 1) {
            return '' . $this->bodyParts;
        }
        
        $ret = '';
        
        if ($headers = $this->getHeaders()) {
            $ret .= "{$headers}\r\n";
        }
        
        $boundary = $this->getBoundaryName();
        return "{$ret}--{$boundary}\r\n" . $this->bodyParts . "\r\n" . $this->renderParts();
    }
}
