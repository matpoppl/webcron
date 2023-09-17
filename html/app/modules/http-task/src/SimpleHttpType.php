<?php

namespace matpoppl\HttpCronTask;

class SimpleHttpType
{
    public static function saninizeHttpHeaders($value)
    {
        $lines = [];
        
        foreach (explode("\n", $value) as $line) {
            $parts = preg_split('#[\s=:]+#', $line, 2);
            $lines[] = implode(': ', $parts);
        }
        
        return implode("\n", $lines);
    }
    
    public function getInputFilter()
    {
        return [
            'url' => [
                'filters' => [
                    'StringTrim',
                ],
                'validators' => [
                    [
                        'type' => 'StringLength',
                        'options' => ['min' => 4, 'max' => 255],
                    ], [
                        'type' => 'Url',
                        'options' => ['allowed_schemas' => ['http', 'https']],
                    ],
                ],
            ],
            'method' => [
                'filters' => [
                    'StringTrim',
                ],
                'validators' => [
                    [
                        'type' => 'StringLength',
                        'options' => ['min' => 3, 'max' => 4],
                    ], [
                        'type' => 'InArray',
                        'options' => ['haystack' => ['GET', 'POST']],
                    ],
                ],
            ],
            
            'ssl' => [
                'inputs' => [
                    'verify_peer' => [
                        'filters' => ['ToInt'],
                    ],
                    'verify_peer_name' => [
                        'filters' => ['ToInt'],
                    ],
                    'allow_self_signed' => [
                        'filters' => ['ToInt'],
                    ],
                ],
            ],
            
            'headers' => [
                'filters' => [
                    'StringTrim',
                    [
                        'type' => 'Callback',
                        'options' => [
                            'callback' => [self::class, 'saninizeHttpHeaders'],
                        ],
                    ],
                    'ToNull',
                ],
                'validators' => [
                    [
                        'type' => 'StringLength',
                        'options' => ['max' => 1000],
                    ],
                ],
            ],
            'body' => [
                'filters' => [
                    'StringTrim',
                    'ToNull',
                ],
                'validators' => [
                    [
                        'type' => 'StringLength',
                        'options' => ['max' => 1000],
                    ],
                ],
            ],
        ];
    }
    
    public function getForm()
    {
        return [
            'method' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'select',
                    'maxlength' => 256,
                ],
                'options' => [
                    'label' => 'HTTP method',
                    'multiOptions' => [
                        'GET' => 'GET',
                        'POST' => 'POST',
                    ],
                ],
            ],
            'url' => [
                'type' => 'text',
                'attributes' => [
                    'class' => 'input-text',
                    'type' => 'url',
                    'required' => true,
                ],
                'options' => [
                    'label' => 'URL',
                ],
            ],
            'headers' => [
                'type' => 'textarea',
                'attributes' => [
                    'class' => 'input-text',
                    'maxlength' => 1001,
                ],
                'options' => [
                    'label' => 'Request headers',
                ],
                'messages' => [
                    'desc' => ['Paste valid HTTP headers'],
                ],
            ],
            'body' => [
                'type' => 'textarea',
                'attributes' => [
                    'class' => 'input-text',
                    'maxlength' => 1001,
                ],
                'options' => [
                    'label' => 'Request body',
                ],
                'messages' => [
                    'desc' => ['Paste valid HTTP body'],
                ],
            ],
            'ssl' => [
                'type' => 'fieldset',
                'options' => [
                    'legend' => 'SSL',
                ],
                'attributes' => [
                    'class' => ' form__fieldset',
                ],
                'elements' => [
                    'verify_peer' => [
                        'type' => 'checkbox',
                        'attributes' => [
                            'class' => 'input-checkbox',
                        ],
                        'options' => [
                            'label' => 'verify peer',
                        ],
                    ],
                    'verify_peer_name' => [
                        'type' => 'checkbox',
                        'attributes' => [
                            'class' => 'input-checkbox',
                        ],
                        'options' => [
                            'label' => 'verify peer name',
                        ],
                    ],
                    'allow_self_signed' => [
                        'type' => 'checkbox',
                        'attributes' => [
                            'class' => 'input-checkbox',
                        ],
                        'options' => [
                            'label' => 'allow self signed',
                        ],
                    ],
                ],
            ],
        ];
    }
}
