<?php

namespace matpoppl\ImageCaptcha;

class ImageCaptcha
{
    private $options;
    
    public function __construct(array $options)
    {
        $this->options = $options;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
}
