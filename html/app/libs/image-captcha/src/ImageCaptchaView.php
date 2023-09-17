<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\Form\Render\HTMLAttributes;
use matpoppl\Form\View\AbstractControlView;

class ImageCaptchaView extends AbstractControlView
{
    public function renderView()
    {
        $elem = $this->getElement();
        
        $attrs = HTMLAttributes::create($elem->getAttributes()->getArrayCopy());
        $attrs->set('type', 'text');

        $imgAttrs = new HTMLAttributes([
            'src' => '/captcha/image.png',
            'width' => 500,
            'height' => 100,
            'alt' => 'Image captcha',
            'loading' => 'lazy',
        ]);
        
        return '<button type="button" onclick="this.firstChild.src+=(\'?\'+performance.now())"><img' . $imgAttrs . '/></button><input' . $attrs . ' />';
    }
}
