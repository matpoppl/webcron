<?php

namespace matpoppl\Translate\Source;

class SourceManager
{
    /** @var SourceFactory */
    private $sourceFactory;
    private $sources;
    private $domains = [];
    
    public function __construct(array $options, SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
        $this->sources = $options['sources'] ?? [];
        
        foreach ($this->sources as $key => $source) {
            $locales = $source['locales'] ?? [];
            foreach ($locales as $locale => $domains) {
                foreach ($domains as $domain) {
                    $this->domains[$locale .'|'. $domain] = $key;
                }
            }
        }
    }
    
    public function has(string $domain, string $locale)
    {
        $key = $locale .'|'. $domain;
        return array_key_exists($key, $this->domains);
    }
    
    public function get(string $domain, string $locale)
    {
        $key = $locale .'|'. $domain;
        
        if (! $this->has($domain, $locale)) {
            throw new \DomainException('Source dont exists ' . $key);
        }
        
        $key = $this->domains[$key];
        
        if (is_array($this->sources[$key])) {
            $this->sources[$key] = $this->sourceFactory->create($this->sources[$key]);
        }
        
        return $this->sources[$key];
    }
}
