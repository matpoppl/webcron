<?php

namespace matpoppl\Email;

use matpoppl\Form\FormBuilder;
use matpoppl\InputFilter\InputFilterBuilder;

class TransportModel
{
    private $optionsDriver = [
        'mail' => 'mail()',
        'smtp' => 'SMTP',
    ];

    private $optionsPort = [
        25 => 25,
        465 => 465,
        587 => 587,
    ];

    private $optionsEncryption = [
        'ssl' => 'SSL',
        'tls' => 'TLS',
        'starttls' => 'STARTTLS',
    ];
    
    private $optionsAuth = [
        'plain' => 'PLAIN',
        'login' => 'LOGIN',
        'cram-md5' => 'CRAM-MD5',
    ];
    
    /**
     * 
     * @param FormBuilder $formBuilder
     * @param array $formDefaults
     * @return \matpoppl\Form\Form
     */
    public function buildForm(FormBuilder $formBuilder, array $attrs, $formDefaults)
    {
        return $formBuilder->createForm([
            'attributes' => $attrs,
            'elements' => [
                'model' => [
                    'type' => 'Fieldset',
                    'elements' => [
                        'name' => [
                            'type' => 'input',
                            'options' => [
                                'label' => 'name',
                            ],
                            'attributes' => [
                                'class' => 'input-text',
                                'required' => true,
                            ],
                        ],
                        'email' => [
                            'type' => 'input',
                            'options' => [
                                'label' => 'e-mail',
                            ],
                            'attributes' => [
                                'class' => 'input-text',
                                'type' => 'email',
                                'required' => true,
                            ],
                        ],
                        'driver' => [
                            'type' => 'Select',
                            'options' => [
                                'label' => 'driver',
                                'multiOptions' => ['' => '-- choose --'] + $this->optionsDriver,
                            ],
                            'attributes' => [
                                'class' => 'input-select',
                                'required' => true,
                            ],
                        ],
                        'hostname' => [
                            'type' => 'input',
                            'options' => [
                                'label' => 'hostname',
                            ],
                            'attributes' => [
                                'class' => 'input-text',
                            ],
                        ],
                        'port' => [
                            'type' => 'Select',
                            'options' => [
                                'label' => 'port',
                                'multiOptions' => ['' => '-- choose --'] + $this->optionsPort,
                            ],
                            'attributes' => [
                                'class' => 'input-select',
                            ],
                        ],
                        'encryption' => [
                            'type' => 'Select',
                            'options' => [
                                'label' => 'encryption',
                                'multiOptions' => ['' => '-- none --'] + $this->optionsEncryption,
                            ],
                            'attributes' => [
                                'class' => 'input-select',
                            ],
                        ],
                        'auth' => [
                            'type' => 'Select',
                            'options' => [
                                'label' => 'auth',
                                'multiOptions' => ['' => '-- none --'] + $this->optionsAuth,
                            ],
                            'attributes' => [
                                'class' => 'input-select',
                            ],
                        ],
                        'username' => [
                            'type' => 'input',
                            'options' => [
                                'label' => 'username',
                            ],
                            'attributes' => [
                                'class' => 'input-text',
                            ],
                        ],
                        'password' => [
                            'type' => 'input',
                            'options' => [
                                'label' => 'password',
                            ],
                            'attributes' => [
                                'class' => 'input-text',
                                'type' => 'password',
                            ],
                        ],
                    ]
                ],
                'btnSubmit' => [
                    'type' => 'button',
                    'options' => [
                        'label' => 'save',
                    ],
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit',
                    ],
                ],
            ],
        ], $formDefaults);
    }
    
    public function buildInputFilter(InputFilterBuilder $builder)
    {
        return $builder->createInputFilter([
            'inputs' => [
                'model' => [
                    'inputs' => [
                        'name' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'email' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 6, 'max' => 255],
                                ],
                                [
                                    'type' => 'EmailAddress',
                                    'options' => ['check_mx' => true],
                                ],
                            ],
                        ],
                        'driver' => [
                            'required' => true,
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                                [
                                    'type' => 'InArray',
                                    'options' => ['haystack' => array_keys($this->optionsDriver)],
                                ],
                            ],
                        ],
                        'hostname' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'port' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'encrypt' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'auth' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'username' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                        'password' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                [
                                    'type' => 'StringLength',
                                    'options' => ['min' => 2, 'max' => 255],
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ]);
    }
}
