<?php

namespace matpoppl\InputFilter;

use matpoppl\Translate\TranslatorInterface;

class Input implements InputInterface
{
    /** @var bool */
    private $required;
    /** @var bool */
    private $multiple;
    /** @var mixed */
    private $value = null;
    /** @var callable */
    private $filters;
    /** @var callable */
    private $validators;
    /** @var string[] */
    private $messages = [];
    
    public function __construct(array $options)
    {
        $this->required = $options['required'] ?? false;
        $this->multiple = $options['multiple'] ?? false;
        $this->filters = $options['filters'] ?? null;
        $this->validators = $options['validators'] ?? null;
    }
    
    public function isValid($ctx = null) : bool
    {
        if (! $this->required && null === $this->value) {
            return true;
        }
        
        $validators = $this->validators;
        
        if (! $validators) {
            return true;
        }
        
        if (! $this->required && null === $this->value) {
            return true;
        }
        
        if ($this->multiple) {
            $hasErrors = false;
            foreach ($this->value as $val) {
                $hasErrors = $validators($val, $ctx);
                if ($hasErrors) {
                    break;
                }
            }
        } else {
            $hasErrors = $validators($this->value, $ctx);
        }
        
        if (false !== $hasErrors) {
            $this->messages = is_array($hasErrors) ? $hasErrors : [$hasErrors];
            return false;
        }
        
        return true;
    }
    
    public function getMessages() : array
    {
        return $this->messages;
    }
    
    public function getTranslatedMessages(TranslatorInterface $translator) : array
    {
        $ret = [];
        foreach ($this->messages as $msg) {
            if (is_array($msg)) {
                $ret[] = $translator->translate(...$msg);
            } else {
                $ret[] = $translator->translate($msg);
            }
        }
        return $ret;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($value)
    {
        if (null === $value) {
            $this->value = $this->multiple ? [] : $value;
            return $this;
        }
        
        if ($this->multiple && !is_array($value)) {
            $value = [$value];
        }
        
        if ($filters = $this->filters) {
            if ($this->multiple) {
                $value = array_map($filters, $value);
            } else {
                $value = $filters($value);
            }
        }
        
        $this->value = $value;
        
        return $this;
    }
}
