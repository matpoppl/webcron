<?php

namespace matpoppl\Mailer;

use matpoppl\Mailer\Part\Parts;
use matpoppl\Mailer\Part\StreamPart;
use matpoppl\Mailer\Part\PartInterface;

class Message
{
    /** @var Headers */
    private $headers;
    /** @var Parts */
    private $parts = [];
    
    public function __construct($headers = null)
    {
        $this->headers = ($headers instanceof Headers) ? $headers : new Headers($headers);
        $this->parts = new Parts();
        
        $this->headers->set('MIME-Version', '1.0');
        $this->headers->set('Content-Transfer-Encoding', '8bit');
        //$this->headers->set('Date', date(DATE_RFC1123));
    }
    
    /** @return \matpoppl\Mailer\Headers */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function setFrom($email, $name = null)
    {
        return $this->headers->setEmail('From', $email, $name);
    }
    
    public function setTo($email, $name = null)
    {
        return $this->headers->setEmail('To', $email, $name);
    }
    
    public function setReturnPath($email, $name = null)
    {
        return $this->headers->setEmail('Return-Path', $email, $name);
    }
    
    public function setReplyTo($email, $name = null)
    {
        return $this->headers->setEmail('Reply-To', $email, $name);
    }
    
    public function setErrorsTo($email, $name = null)
    {
        return $this->headers->setEmail('Errors-To', $email, $name);
    }
    
    public function setSubject($subject)
    {
        $this->headers->set('Subject', $subject);
        return $this;
    }
    
    public function setBody($body, $contentType, $charset)
    {
        $this->parts->setBody($body, $contentType, $charset);
        return $this;
    }
    
    public function addBody($body, $contentType, $charset)
    {
        $this->parts->addBody($body, $contentType, $charset);
        return $this;
    }
    
    public function attachFile($pathname, $contentType)
    {
        return $this->addPart(StreamPart::createFromPathname($pathname, $contentType));
    }
    
    public function addPart(PartInterface $part)
    {
        $this->parts->add($part);
        return $this;
    }
    
    public function __toString()
    {
        return $this->headers . $this->parts;
    }
}
