<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\Form\Element\AbstractControlElement;

class ImageCaptchaElement extends AbstractControlElement
{
    public function getViewType(): string
    {
        return ImageCaptchaView::class;
    }
}
