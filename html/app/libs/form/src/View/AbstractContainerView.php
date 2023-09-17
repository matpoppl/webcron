<?php
namespace matpoppl\Form\View;

use matpoppl\Form\Element\ContainerInterface;

abstract class AbstractContainerView extends AbstractElementView
{
    /** @var ContainerInterface */
    protected $element;
    
    /** @var ElementlViewInterface[] */
    private $elements = [];
    
    /** @var ElementViewFactory */
    private $factory = null;
    
    abstract public function renderContainerStart();
    abstract public function renderContainerEnd();
    
    public function has($name)
    {
        return $this->element->has($name);
    }
    
    /**
     * 
     * @param string $name
     * @return ElementlViewInterface
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->elements)) {
            return $this->elements[$name];
        }
        
        $elem = $this->element->get($name);
        
        $viewElem = $this->getFactory()->create($elem);
        
        if ($viewElem instanceof self) {
            $viewElem->setFactory($this->getFactory());
        }
        
        $this->elements[$name] = $viewElem;
        
        return $this->elements[$name];
    }
    
    public function __isset($name): bool
    {
        return $this->has($name);
    }
    
    public function __get($name): mixed
    {
        return $this->get($name);
    }
    
    public function renameElements()
    {
        $attrs = $this->element->getAttributes();
        
        $baseId = $attrs['id'];
        $baseName = $attrs['name'] ?? '';
        
        $hasBaseId = strlen($baseId) > 0;
        $hasBaseName = strlen($baseName) > 0;
        
        foreach ($this->element->getFieldList() as $name) {
            $elem = $this->get($name);
            
            $elem['name'] = $hasBaseName ? "{$baseName}[{$name}]" : $name;
            $elem['id'] = $hasBaseId ? "{$baseId}-{$name}" : $name;
            
            if ($elem instanceof AbstractContainerView) {
                $elem->renameElements();
            }
        }
        
        return $this;
    }
    
    public function renderView()
    {
        return $this->renderFormRow();
    }
    
    public function renderFormRow(array $attrs = null)
    {
        $msgs = '';
        foreach ($this->element->getMessageTypes() as $type) {
            $msgs .= $this->renderMessagesOf($type);
        }
        
        if (!empty($msgs)) {
            $msgs = '<div class="form__messages">' . $msgs . '</div>';
        }
        
        return $this->renderContainerStart($attrs) . $msgs . $this->renderElements() . $this->renderContainerEnd();
    }
    
    public function renderElements()
    {
        $this->renameElements();
        
        $ret = '';
        
        foreach ($this->element->getFieldList() as $name) {
            /** @var ElementlViewInterface $elem */
            $ret .= $this->{$name}->renderFormRow();
        }
        
        return $ret;
    }
    
    /** @return ElementViewFactory */
    public function getFactory()
    {
        if (null === $this->factory) {
            $this->setFactory(new ElementViewFactory($this->container));
        }
        return $this->factory;
    }
    
    public function setFactory($factory)
    {
        $this->factory = $factory;
        return $this;
    }
}
