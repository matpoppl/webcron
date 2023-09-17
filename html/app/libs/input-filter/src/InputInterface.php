<?php

namespace matpoppl\InputFilter;

use matpoppl\Translate\TranslatorInterface;

interface InputInterface
{
    public function getValue();
    public function setValue($value);
    public function isValid($ctx = null) : bool;
    public function getMessages() : array;
    public function getTranslatedMessages(TranslatorInterface $translator) : array;
}
