<?php

namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;

class GuestController extends AbstractController
{
    public function indexAction()
    {
        return $this->redirect($this->view->route('signin'));
    }
    
    public function signinAction()
    {
        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'username' => [
                    'filters' => [
                        'StringTrim',
                    ],
                    'validators' => [
                        [
                            'type' => 'StringLength',
                            'options' => ['min' => 2, 'max' => 255],
                        ],
                    ],
                ],
                'password' => [
                    'filters' => [
                        'StringTrim',
                    ],
                    'validators' => [
                        [
                            'type' => 'StringLength',
                            'options' => ['min' => 2, 'max' => 255],
                        ],
                    ],
                ],
                'rememberme' => [
                    'filters' => ['StringTrim']
                ],
                'csrf' => [
                    'validators' => [
                        [
                            'type' => 'Equals',
                            'options' => ['expected' => $this->container->get('csrf.manager')->getHash()],
                        ],
                    ],
                ],
            ],
        ]);

        /** @var \matpoppl\Form\Form $form */
        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'class' => 'form form--signin',
                'method' => 'post',
                'autocomplete' => 'off',
            ],
            'elements' => [
                'username' => [
                    'type' => 'text',
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true,
                        'autofocus' => true,
                    ],
                    'options' => [
                        'label' => 'username',
                    ],
                ],
                'password' => [
                    'type' => 'password',
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true,
                    ],
                    'options' => [
                        'label' => 'password',
                    ],
                ],
                'rememberme' => [
                    'type' => 'checkbox',
                    'attributes' => [
                        'class' => 'input-checkbox',
                    ],
                    'options' => [
                        'label' => 'persist signin session',
                    ],
                ],
                'submit' => [
                    'type' => 'button',
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit',
                    ],
                    'options' => [
                        'label' => 'save',
                    ],
                ],
            ],
        ]);
        
        if ('POST' === $this->request->getMethod()) {
            $errors = [];
            
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            
            if ($inputFilter->isValid($post)) {
                
                /** @var \App\Security\IdentityManager $im */
                $im = $this->container->get('identity.manager');
                
                /** @var \matpoppl\SmallMVC\Security\AuthResultInterface $result */
                $result = $im->signin(
                    $this->container->get('auth.manager'),
                    $inputFilter->get('username')->getValue(),
                    $inputFilter->get('password')->getValue()
                );
                
                if ($result->hasIdentity()) {                
                    $sm = $this->container->get('session.manager');
                    
                    $sm->regenerateID();
                    $sm->close();
                    
                    if ($inputFilter->get('rememberme')->getValue()) {
                        $sm->setCookieLifetime( 86400 * 356 );
                    } else {
                        $sm->setCookieLifetime( 0 );
                    }
                    
                    $sm->start();
                    
                    $safeRoute = $im->getSafeRouteFor();
                    
                    if (null === $safeRoute) {
                        throw new \UnexpectedValueException('Role without safe route');
                    }
                    
                    return $this->redirect($this->view->route($safeRoute));
                }
                
                //var_dump($result->getCode(), $result->getMessage());

                $errors[] = $result->getMessage();
            }
            
            $translator = $this->container->get('translator');
            $errors = array_merge($errors, $inputFilter->getTranslatedMessages($translator));
            
            $form->setValue($inputFilter->getValue())
            ->setMessagesOf('error', $errors);
            $this->response = $this->response->withStatus(400);
        }

        $this->view->meta->title('Sign in');

        return $this->render('guest/signin.phtml', [
            'form' => $form->getView(),
        ]);
    }
    
    public function passwordResetAction()
    {
        /** @var \matpoppl\Form\Form $form */
        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'class' => 'form form--signin',
                'method' => 'post',
                'autocomplete' => 'off',
            ],
            'elements' => [
                'email' => [
                    'type' => 'email',
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true,
                        'autofocus' => true,
                    ],
                    'options' => [
                        'label' => 'e-mail address',
                    ],
                ],
                'captcha' => [
                    /** @see \matpoppl\ImageCaptcha\ImageCaptchaElement */
                    'type' => 'ImageCaptcha',
                    'messages' => [
                        'desc' => [
                            'Type letters from image',
                        ],
                    ],
                    'attributes' => [
                        'class' => 'input-text',
                        'required' => true,
                    ],
                    'options' => [
                        'label' => 'image captcha',
                    ],
                ],
                'btnSubmit' => [
                    'type' => 'button',
                    'attributes' => [
                        'class' => 'btn btn--primary',
                        'type' => 'submit',
                    ],
                    'options' => [
                        'label' => 'reset',
                    ],
                ],
            ],
        ]);
        
        if ('POST' === $this->request->getMethod()) {
            
            /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
            $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
                'inputs' => [
                    'email' => [
                        'filters' => [
                            'StringTrim',
                        ],
                        'validators' => [
                            [
                                'type' => 'StringLength',
                                'options' => ['min' => 6, 'max' => 255],
                            ],
                            /** @see \matpoppl\DataValidator\EmailAddressValidator */
                            'EmailAddress',
                        ],
                    ],
                    'captcha' => [
                        'filters' => [
                            'StringTrim',
                        ],
                        'validators' => [
                            [
                                'type' => 'StringLength',
                                'options' => ['min' => 2, 'max' => 255],
                            ], [
                                /** @see \matpoppl\ImageCaptcha\ImageCaptchaValidator */
                                'type' => 'ImageCaptcha',
                            ],
                        ],
                    ],
                ],
            ]);
            
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            
            if ($inputFilter->isValid($post)) {
                
                var_dump($inputFilter->getValue());
                die();
                
                $specs = $mailing->getRenderCtxFor('guest/password-reset');
                
                $output = $specs->render('guest/password-reset', [
                    '{RESET_URL}' => $this->view->route('auth/password-change', [
                        'token' => $authManger->createPasswordResetToken(),
                    ]),
                ]);
                
                $mailer = $mailerFactory->create($specs->transporter);
                
                $msg = $mailer->createMessage()
                ->from($specs->from)
                ->to($specs->to)
                ->subject($specs->subject)
                ->body($output->txt)
                ->body($output->html);
                
                $mailer->send($msg);
            }
            
            $form->setValue($inputFilter->getValue());
            $form->setMessagesOf('error', $inputFilter->getTranslatedMessages($this->container->get('translator')));
        }
        
        return $this->render('guest/reset.phtml', [
            'form' => $form->getView(),
        ]);
    }
}
