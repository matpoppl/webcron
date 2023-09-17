<?php

namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;
use App\Entity\TranslationEntity;

class TranslateController extends AbstractController
{
    public function indexAction()
    {
        $this->view->meta->title('Translations index');

        $this->container->get('translator');

        $form = $this->container->get('form.builder')->createForm([
            'attributes' => [
                'method' => 'post',
                'action' => $this->view->route('translation/edit'),
            ],
            'elements' => [
                'translation' => [
                    'type' => 'Fieldset',
                    'options' => [
                        'label' => 'Add translation',
                        'legend' => 'Add translation',
                    ],
                    'elements' => [
                        'msgid' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'Translation source',
                            ],
                        ],
                        'value' => [
                            'type' => 'Input',
                            'options' => [
                                'label' => 'Translation target',
                            ],
                        ],
                        'submit' => [
                            'type' => 'Button',
                            'attributes' => [
                                'type' => 'submit',
                                'class' => 'btn btn--primary',
                            ],
                            'options' => [
                                'label' => 'Save',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');

        return $this->render('translate/index.phtml', [
            'rows' => $em->fetchRows(TranslationEntity::class, 'dbal_stmt', []),
            'form' => $form->getView(),
        ]);
    }

    public function editAction()
    {
        $id = (int) $this->request->get('id');

        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $this->container->get('entity.manager');

        if ($id > 0) {
            $entity = $em->find(TranslationEntity::class, $id);

            if (! $entity) {
                throw new \UnexpectedValueException('Entity dont exists');
            }
        } else {
            $entity = new TranslationEntity();
        }

        /** @var \matpoppl\InputFilter\InputFilter $inputFilter */
        $inputFilter = $this->container->get('input.filter.builder')->createInputFilter([
            'inputs' => [
                'translation' => [
                    'inputs' => [
                        'msgid' => [
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
                        'value' => [
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
                    ],
                ],
            ],
        ]);

        if ('POST' === $this->request->getMethod()) {
            $post = $this->request->getParsedBody();
            $inputFilter->setValue($post);
            if ($inputFilter->isValid($post)) {
                $data = $inputFilter->getValue();
                $data['translation']['domain'] = 'default';
                $data['translation']['locale'] = 'pl_PL';

                $em->getEntitySpecs($entity)->getHydrator()->hydrate($data['translation'], $entity);

                if (! $em->save($entity)) {
                    throw new \UnexpectedValueException('Entity save error');
                }

                $this->view->flashMessenger->add('success', 'Saved');
            }
        }

        return $this->redirect($this->view->route('translations'));
    }
}
