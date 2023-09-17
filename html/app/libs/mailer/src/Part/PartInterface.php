<?php

namespace matpoppl\Mailer\Part;

use matpoppl\Mailer\Headers;

interface PartInterface
{
    /** @return Headers */
    public function getHeaders() ;
    /** @return string */
    public function getContents();
}
