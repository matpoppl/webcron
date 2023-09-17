<?php

namespace matpoppl\Mailer\Transport;

use matpoppl\Http\Message\Headers;

interface MessageInterface
{
    public function setFrom($email, $name = null);
    
    public function setTo($email, $name);
    
    public function setSubject($subject);
    
    public function setBody($body, $contentType = null, $charset = null);
    
    public function addPart($body, $contentType = null, $charset = null);
}
