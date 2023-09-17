<?php

namespace matpoppl\Form\Element;

use matpoppl\Form\View\DateTimeView;

class DateTimeElement extends AbstractControlElement
{
    /** @return InputElement */        
    public function getDateElement()
    {
        $id = $this->getAttributes()->get('id');
        $name = $this->getAttributes()->get('name');
        $value = $this->getValue();
        
        $elem = new InputElement($this->container, [
            'attributes' => [
                'type' => 'date',
                'id' => $id,
                'name' => $name . '[date]',
            ],
        ]);
        
        $elem->setValue(substr($value, 0, 10));
        
        return $elem;
    }
    
    /** @return InputElement */   
    public function getTimeElement()
    {
        $id = $this->getAttributes()->get('id');
        $name = $this->getAttributes()->get('name');
        $value = $this->getValue();
        
        $elem = new InputElement($this->container, [
            'attributes' => [
                'type' => 'time',
                'id' => $id . '-time',
                'name' => $name . '[time]',
                'step' => $this->getOptions()->get('time-step', 60),
            ],
        ]);
        
        $elem->setValue(substr($value, 11));
        
        return $elem;
    }
    
    public function getValue()
    {
        $value = parent::getValue();
        
        if (is_array($value)) {
            return $value['date'] . ' ' . $value['time'];
        }
        
        return null;
    }
    
    public function setValue($value)
    {
        if (! $value) {
            return parent::setValue(null);
        }
        
        if (is_array($value)) {
            return parent::setValue($value);
        }
        
        if (is_string($value)) {
            return parent::setValue([
                'date' => substr($value, 0, 10),
                'time' => substr($value, 11),
            ]);
        }
        
        throw new \UnexpectedValueException('Unsupported datetime value');
    }
    
    public function setMessagesOf(string $type, array $msgs)
    {
        if (array_key_exists('date', $msgs) || array_key_exists('time', $msgs)) {
            if (count(array_filter($msgs)) > 0) {
                parent::setMessagesOf($type, $msgs);
            }
        }
        
        return $this;
    }
    
    public function getViewType(): string
    {
        return DateTimeView::class;
    }
}
