<?php

namespace matpoppl\DataValidator;

use matpoppl\ServiceManager\Factory\InvokableFactory;
use matpoppl\SmallMVC\Module\ModuleInterface;
use matpoppl\SmallMVC\Application;

class DataValidatorModule implements ModuleInterface
{
    public function init(Application $app)
    {}
    
    public function getConfig(): array
    {
        return [
            'service_manager' => [
                'aliases' => [
                    'data.validator.Callback' => CallbackValidator::class,
                    'data.validator.StringLength' => StringLengthValidator::class,
                    'data.validator.Equals' => EqualsValidator::class,
                    'data.validator.InArray' => InArrayValidator::class,
                    'data.validator.Url' => UrlValidator::class,
                    'data.validator.Match' => MatchValidator::class,
                    'data.validator.NumberBetween' => NumberBetweenValidator::class,
                    'data.validator.DateTimeFormat' => DateTimeFormatValidator::class,
                    'data.validator.EmailAddress' => EmailAddressValidator::class,
                    'data.validator.Csrf' => CsrfValidator::class,
                ],
                'factories' => [
                    CallbackValidator::class => InvokableFactory::class,
                    StringLengthValidator::class => InvokableFactory::class,
                    EqualsValidator::class => InvokableFactory::class,
                    InArrayValidator::class => InvokableFactory::class,
                    UrlValidator::class => InvokableFactory::class,
                    MatchValidator::class => InvokableFactory::class,
                    NumberBetweenValidator::class => InvokableFactory::class,
                    DateTimeFormatValidator::class => InvokableFactory::class,
                    EmailAddressValidator::class => InvokableFactory::class,
                    CsrfValidator::class => InvokableFactory::class,
                ],
            ],
        ];
    }

}
