<?php

namespace matpoppl\DataValidator;

class UrlValidator implements ValidatorInterface
{
    /** @val int|NULL */
    private $flags;
    /** @val string[]|NULL */
    private $allowedSchemes;
    
    public function __construct(array $options = null)
    {
        if (null === $options) {
            return;
        }
        
        $this->flags = $options['flags'] ?? null;
        $this->allowedSchemes = $options['allowed_schemes'] ?? null;
    }
    
    public function __invoke($data, $ctx = null)
    {
        $filtered = null === $this->flags ? filter_var($data, FILTER_VALIDATE_URL) : filter_var($data, FILTER_VALIDATE_URL, $this->flags);
        
        if ($data !== $filtered) {
            return 'Invalid URL format';
        }
        
        $parts = parse_url($filtered);
        
        if (! is_array($parts)) {
            return 'Invalid URL format';
        }
        
        if (is_array($this->allowedSchemes)) {
            if (! isset($parts['scheme'])) {
                return 'scheme in URL is required';
            }
            if (! in_array($parts['scheme'], $this->allowedSchemes)) {
                return ['Unsupported scheme `{scheme}`. Only supporting `{allowed}`', ['{scheme}' => $parts['scheme'], '{allowed}' => implode(', ', $this->allowedSchemes)], 'data_validator.Url'];
            }
        }
        
        return false;
    }
}
