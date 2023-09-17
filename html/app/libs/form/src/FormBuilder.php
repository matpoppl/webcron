<?php
namespace matpoppl\Form;

use Psr\Container\ContainerInterface;

class FormBuilder
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * 
     * @param array $options
     * @param array $data
     * @return \matpoppl\Form\Form
     */
    public function createForm(array $options, array $data = null)
    {
        $form = new Form($this->container, $options);
        
        // @TODO add csrf
        //user_error('@TODO add csrf', E_USER_NOTICE);
        $form->set('csrf', ['type' => 'Csrf']);
        
        if (null !== $data) {
            $form->setValue($data);
        }
        
        return $form;
    }
    
    /** @return ContainerInterface */
    public function getServiceContainer()
    {
        return $this->container;
    }
}
