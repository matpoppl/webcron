<?php

namespace matpoppl\Translate;

use matpoppl\Intl\LocaleInterface;

class Translator implements TranslatorInterface
{
    /** @var LocaleInterface */
    private $locale;
    /** @var Source\SourceManager */
    private $sources;
    
    public function __construct(LocaleInterface $locale, Source\SourceManager $sources)
    {
        $this->locale = $locale;
        $this->sources = $sources;
    }
    
    public function getDomain(string $domain = null, string $locale = null)
    {
        return $this->sources->get($domain ?: 'default', $locale ?: $this->locale->getLocale());
    }
    
    public function translate(string $msgid, array $params = null, string $domain = null, string $locale = null) : string
    {
        $msg = $this->getDomain($domain, $locale)->get($msgid);
        return null === $params ? $msg : sprintf($msg, ...$params);
    }
}
