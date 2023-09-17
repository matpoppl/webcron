<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\DataValidator\ValidatorInterface;

class ImageCaptchaValidator implements ValidatorInterface
{
    /** @var PhraseSourceInterface */
    private $phraseSource;
    
    public function __construct(PhraseSourceInterface $phraseSource)
    {
        $this->phraseSource = $phraseSource;
    }
    
    public function __invoke($data, $ctx = null)
    {
        $expected = $this->phraseSource->getAndTick();
        
        if ($data !== $expected) {
            return [['CAPTCHA phrase mismatch `{0}`/`{1}`', ['{0}' => $data, '{1}' => $expected]]];
        }
        
        return false;
    }
}
