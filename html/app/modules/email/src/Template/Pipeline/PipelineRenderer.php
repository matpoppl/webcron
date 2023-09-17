<?php

namespace matpoppl\Email\Template\Pipeline;

use matpoppl\Email\Entity\TemplateEntity;

class PipelineRenderer
{
    /**
     * 
     * @param TemplateEntity[] $entity
     * @param array $vars
     * @throws \UnexpectedValueException
     * @return string[]
     */
    public function render(array $entities, array $vars)
    {
        if (empty($entities)) {
            throw new \UnexpectedValueException('Template entities required');
        }
        
        foreach ($entities as $entity) {
            
            if (! ($entity instanceof TemplateEntity)) {
                throw new \UnexpectedValueException('Template entitiy required');
            }
            
            $txt = (null === $entity->contentTxt) ? '' : strtr($entity->contentTxt, $vars);
            $html = (null === $entity->contentHtml) ? '' : strtr($entity->contentHtml, $vars);
            
            $vars['WRAPPED_CONTENT_TXT'] = $txt;
            $vars['WRAPPED_CONTENT_HTML'] = $html;
        }
        
        return [$txt, $html];
    }
}
