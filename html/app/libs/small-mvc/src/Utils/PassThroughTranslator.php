<?php

namespace matpoppl\SmallMVC\Utils;

use matpoppl\Translate\TranslatorInterface;

class PassThroughTranslator implements TranslatorInterface
{
    public function translate(string $msgid, array $params = null, string $domain = null, string $locale = null): string
    {
        if (null === $params) {
            return $msgid;
        }
        
        return strtr($msgid, $params);
    }
}
