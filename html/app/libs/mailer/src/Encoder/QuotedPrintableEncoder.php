<?php

namespace matpoppl\Mailer\Encoder;

class QuotedPrintableEncoder implements EncoderInterface
{
    private $charset = 'UTF-8';
    
    public function encode($str)
    {
        if (null === $str || ctype_print($str)) {
            return $str;
        }
        $encoded = quoted_printable_encode($str);
        return "=?{$this->charset}?Q?{$encoded}?=";
    }
}
