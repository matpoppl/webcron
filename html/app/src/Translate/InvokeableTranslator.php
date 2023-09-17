<?php

namespace App\Translate;

use matpoppl\Translate\TranslatorInterface;

class InvokeableTranslator implements TranslatorInterface
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var string */
    private $domain;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function __invoke(string $msgid, array $params = null, string $domain = null, string $locale = null): string
    {
        return $this->translator->translate($msgid, $params, $domain ?: $this->domain, $locale);
    }
    
    public function translate(string $msgid, array $params = null, string $domain = null, string $locale = null): string
    {
        return $this->translator->translate($msgid, $params, $domain ?: $this->domain, $locale);
    }
    
    public function withDomain($domain)
    {
        $ret = clone $this;
        $ret->domain = $domain;
        return $this;
    }
}
