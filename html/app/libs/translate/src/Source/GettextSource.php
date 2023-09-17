<?php

namespace matpoppl\Translate\Source;

use function gettext;
use function dgettext;
use function putenv;
use function setlocale;
use function bindtextdomain;
use function textdomain;

class GettextSource
{
    /** @var string */
    private $defaultDomain;
    /** @var string */
    private $locale;
    /** @var string */
    private $dir;
    
    public function __construct(array $options)
    {
        $this->locale = $options['locale'] ?? \Locale::getDefault();
        $this->defaultDomain = $options['default_domain'] ?? 'default';
        $this->dir = rtrim($options['dir'] ?? '', '\\/') . '/';
        
        putenv('LC_ALL=' . $this->locale);
        setlocale(LC_ALL, $this->locale);
        
        $domains = $options['domains'] ?? [];
        // expects /de_DE/LC_MESSAGES/myPHPApp.mo
        // expects /{LOCALE}/LC_MESSAGES/{DOMAIN}.mo
        foreach ($domains as $domain) {
            
            $pathname = $this->dir . $this->locale . '/LC_MESSAGES/' . $domain . '.mo';
            
            if (! is_readable($pathname)) {
                throw new \InvalidArgumentException('Gettext domain file access error ' . $pathname);
            }
            
            bindtextdomain($domain, $this->dir);
        }
        
        textdomain($this->defaultDomain);
    }
    
    public function get(string $msgid, string $domain = null)
    {
        return (null === $domain || $this->defaultDomain === $domain) ? gettext($msgid) : dgettext($domain, $msgid);
    }
}
