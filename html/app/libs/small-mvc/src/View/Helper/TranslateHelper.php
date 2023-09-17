<?php

namespace matpoppl\SmallMVC\View\Helper;

use matpoppl\Translate\TranslatorInterface;
use Psr\Container\ContainerInterface;

class TranslateHelper extends AbstractHelper
{
    /** @var TranslatorInterface */
    private $translator;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function __invoke($msgid, array $params = null, $domain = null)
    {
        return $this->translator->translate($msgid, $params, $domain);
    }
    
    public function getFunction()
    {
        $translator = $this->translator;
        return function($msgid, array $params = null, $domain = null) use ($translator) {
            return $translator->translate($msgid, $params, $domain);
        };
    }
    
    public static function create(ContainerInterface $container, ...$args)
    {
        return new static($container->get('translator'), ...$args);
    }
}
