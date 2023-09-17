<?php

namespace matpoppl\ImageCaptcha;

interface PhraseSourceInterface
{
    public function getAndTick() : string;
    
    public function tickAndGet() : string;
}
