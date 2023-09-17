<?php
/**
 * !!File header!!
 */
namespace matpoppl\Form;

use matpoppl\Form\Element\AbstractContainerElement;
use matpoppl\Form\View\FormView;

/**
 * !! Class Header !!
 * @author matpoppl
 *
 */
class Form extends AbstractContainerElement
{
    public function getView()
    {
        static $counter = 0;
        
        if (! $this->attributes->has('id')) {
            $this->attributes->set('id', $this->attributes->get('name', 'form' . ($counter++)));
        }
        
        if ($this->container->has('csrf.manager')) {
            $this->set('csrf', [
                'type' => 'csrf',
                'options' => [
                    'label' => 'CSRF token',
                ],
            ]);
        }
        
        return new View\FormView($this->container, $this);
    }
    
    public function getViewType(): string
    {
        return FormView::class;
    }
}
