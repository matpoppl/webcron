<?php

namespace matpoppl\Email\Entity;

use matpoppl\EntityManager\EntityInterface;

class TemplateEntity implements EntityInterface
{
    /** @var bool */
    private $_isNewRecord = true;
    
    public $id;
    public $sid;
    public $active = 0;
    public $parent;
    
    public $name;
    public $subject;
    
    public $to;
    public $cc;
    public $bcc;
    public $replyTo;
    
    public $contentTxt;
    public $contentHtml;
    
    public function isNewEntity($isNewRecord = null)
    {
        if (null === $isNewRecord) {
            return $this->_isNewRecord;
        }
        
        $this->_isNewRecord = $isNewRecord;
        
        return $this;
    }
}
