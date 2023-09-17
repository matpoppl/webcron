<?php
namespace matpoppl\Email\Form;

use matpoppl\Form\FormBuilder;
use matpoppl\InputFilter\InputFilterBuilder;

class SendModel
{

    public function buildEditForm(FormBuilder $builder)
    {
        /** @var \matpoppl\Email\Template\Manager $tplMgr */
        $tplMgr = $builder->getServiceContainer()->get('email.template.manager');
        
        return $builder->createForm([
            'attributes' => [
                'class' => 'form form--edit',
                'method' => 'post'
            ],
            'elements' => [
                'email' => [
                    'elements' => [
                        'email' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'e-mail address',
                            ],
                            'attributes' => [
                                'type' => 'email',
                                'class' => 'input-text',
                                'required' => true
                            ],
                        ],
                        'subject' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'subject'
                            ],
                            'attributes' => [
                                'type' => 'text',
                                'class' => 'input-text',
                                'required' => true
                            ]
                        ],
                        'to' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'e-mail to field'
                            ],
                            'attributes' => [
                                'type' => 'text',
                                'class' => 'input-text',
                                'required' => true
                            ]
                        ],
                        'cc' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'e-mail cc field'
                            ],
                            'attributes' => [
                                'type' => 'text',
                                'class' => 'input-text',
                                'required' => true
                            ]
                        ],
                        'bcc' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'e-mail bcc field'
                            ],
                            'attributes' => [
                                'type' => 'text',
                                'class' => 'input-text',
                                'required' => true
                            ]
                        ],
                        'replyTo' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'e-mail replyTo field'
                            ],
                            'attributes' => [
                                'type' => 'text',
                                'class' => 'input-text',
                                'required' => true
                            ]
                        ],
                    ],
                ],
                'btnSubmit' => [
                    'type' => 'Button',
                    'options' => [
                        'label' => 'send'
                    ],
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit'
                    ]
                ]
            ]
        ]);
    }

    public function buildInputFilter(InputFilterBuilder $builder)
    {
        return $builder->createInputFilter([
            'inputs' => [
                'entity' => [
                    'inputs' => [
                        'active' => [
                            'filters' => ['ToInt', 'ToNull'],
                        ],
                        'sid' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                ['StringLength', ['min' => 2, 'max' => 50]],
                            ]
                        ],
                        'name' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                ['StringLength', ['min' => 2, 'max' => 200]],
                            ]
                        ],
                        'subject' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                ['StringLength', ['min' => 2, 'max' => 200]],
                            ]
                        ],
                        'parent' => [
                            'required' => true,
                            'filters' => ['ToInt'],
                        ],
                        'contentTxt' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                ['StringLength', ['min' => 2, 'max' => (1 << 16) - 1 ]],
                            ]
                        ],
                        'contentHtml' => [
                            'required' => true,
                            'filters' => ['StringTrim'],
                            'validators' => [
                                ['StringLength', ['min' => 2, 'max' => (1 << 16) - 1 ]],
                            ]
                        ],
                        'to' => [
                            'filters' => ['StringTrim', 'EmailAddressDetailsFilter', 'ToNull'],
                            'validators' => [
                                ['StringLength', ['min' => 6, 'max' => 200]],
                            ]
                        ],
                        'cc' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                ['StringLength', ['min' => 6, 'max' => 200]],
                            ]
                        ],
                        'bcc' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                ['StringLength', ['min' => 6, 'max' => 200]],
                            ]
                        ],
                        'replyTo' => [
                            'filters' => ['StringTrim', 'ToNull'],
                            'validators' => [
                                ['StringLength', ['min' => 6, 'max' => 200]],
                            ]
                        ],
                    ],
                ],
            ]
        ]);
    }
}
