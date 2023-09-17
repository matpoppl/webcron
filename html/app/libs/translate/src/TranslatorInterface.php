<?php

namespace matpoppl\Translate;

interface TranslatorInterface
{
    public function translate(string $msgid, array $params = null, string $domain = null, string $locale = null) : string;
}
