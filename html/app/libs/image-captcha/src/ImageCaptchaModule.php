<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\Form\Element\ElementFactory;
use matpoppl\ServiceManager\Factory\InvokableFactory;
use matpoppl\SmallMVC\Module\ModuleInterface;
use matpoppl\SmallMVC\Application;

class ImageCaptchaModule implements ModuleInterface
{
    public function init(Application $app)
    {}
    
    public function getConfig(): array
    {
        return [
            'service_manager' => [
                'aliases' => [
                    'image_captcha' => ImageCaptcha::class,
                    'image_captcha.controller' => CaptchaController::class,
                    'form.element.ImageCaptcha' => ImageCaptchaElement::class,
                    'data.validator.ImageCaptcha' => ImageCaptchaValidator::class,
                    PhraseSourceInterface::class => PhraseSource::class,
                ],
                'factories' => [
                    ImageCaptchaValidator::class => ImageCaptchaValidatorFactory::class,
                    PhraseSource::class => PhraseSourceFactory::class,
                    ImageCaptchaElement::class => ElementFactory::class,
                    ImageCaptcha::class => ImageCaptchaFactory::class,
                    CaptchaController::class => InvokableFactory::class,
                ],
            ],
        ];
    }
}
