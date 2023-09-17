<?php

namespace matpoppl\Form\Element;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\ServiceManager\ServiceManagerInterface;

class ElementFactory implements ElementFactoryInterface, FactoryInterface
{
    /** @var ServiceManagerInterface */
    private $sm;
    
    public function __construct(ServiceManagerInterface $sm)
    {
        $sm->set(self::class, $this);
        
        $sm->addAliases([
            'form.element.Email' => InputElement::class,
            'form.element.Input' => InputElement::class,
            'form.element.Button' => ButtonElement::class,
            'form.element.Checkbox' => CheckboxElement::class,
            'form.element.Csrf' => CsrfElement::class,
            'form.element.DateTime' => DateTimeElement::class,
            'form.element.Fieldset' => FieldsetElement::class,
            'form.element.Select' => SelectElement::class,
            'form.element.Textarea' => TextareaElement::class,
        ]);
        
        $sm->addFactories([
            InputElement::class => self::class,
            ButtonElement::class => self::class,
            CheckboxElement::class => self::class,
            CsrfElement::class => self::class,
            DateTimeElement::class => self::class,
            FieldsetElement::class => self::class,
            SelectElement::class => self::class,
            TextareaElement::class => self::class,
        ]);
        
        $this->sm = $sm;
    }
    
    public function createElement(array $options): ElementInterface
    {
        if (! isset($options['type'])) {
            $type = isset($options['elements']) ? 'Fieldset' : 'Input';
            return $this->sm->create('form.element.' . ucfirst($type), $options);
        }
        
        $type = $options['type'];
        
        if ($this->sm->has($type)) {
            return $this->sm->create($type, $options);
        }
        
        switch (strtolower($type)) {
            case 'date':
            case 'email':
            case 'number':
            case 'password':
            case 'search':
            case 'tel':
            case 'text':
            case 'time':
            case 'url':
                $alias = 'form.element.Input';
                $options['attributes']['type'] = $type;
                break;
            case 'dateTime':
                $alias = 'form.element.Input';
                $options['attributes']['type'] = 'datetime-local';
                break;
            default:
                $alias = 'form.element.' . ucfirst($type);
        }
        
        return $this->sm->create($alias, $options);
        
    }
    
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        return new $name($container, ...$args);
    }
}
