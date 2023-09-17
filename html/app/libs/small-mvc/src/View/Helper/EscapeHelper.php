<?php

namespace matpoppl\SmallMVC\View\Helper;

class EscapeHelper extends AbstractHelper
{
    public function __invoke($name)
    {
        return htmlspecialchars(''.$name, ENT_QUOTES, 'UTF-8');
    }
}
