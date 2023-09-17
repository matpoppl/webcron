<?php

namespace matpoppl\Intl;

class Locale implements LocaleInterface
{
    /** @var string */
    private $locale;
    
    public function __construct(array $options)
    {
        $this->locale = $options['locale'] ?? \Locale::getDefault();
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
