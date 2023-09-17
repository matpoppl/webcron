<?php

namespace matpoppl\Form\View;

use matpoppl\Form\Render\HTMLAttributes;

/**
 * @property \matpoppl\Form\Element\DateTimeElement $element
 */
class DateTimeView extends AbstractControlView
{
    public function renderMessagesOf($type)
    {
        $msgs = $this->element->getMessagesOf($type);
        
        if (array_key_exists('date', $msgs) || array_key_exists('time', $msgs)) {
            $ret = '';
            foreach ($msgs as $fieldMsgs) {
                $ret .= empty($fieldMsgs) ? '' : ''.implode('</li><li>', array_map(function($str){
                    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
                }, $fieldMsgs)).'';
            }
            return empty($ret) ? '' : '<ul class="form__msg form__msg--'.$type.'"><li>'.$ret.'</li></ul>';
        }
        
        return parent::renderMessagesOf($type);
    }
    
    public function renderView()
    {
        $date = $this->viewElementFactory->create($this->element->getDateElement());
        $time = $this->viewElementFactory->create($this->element->getTimeElement());

        $attrs = HTMLAttributes::create($this->element->getAttributes()->getArrayCopy());

        $attrs['id'] .= '-wrap';
        unset($attrs['name']);
        
        return '<span' . $attrs . '>' . $date->renderView() . $time->renderView() . '</span>';
    }
}
