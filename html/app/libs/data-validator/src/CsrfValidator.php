<?php

namespace matpoppl\DataValidator;

class CsrfValidator implements ValidatorInterface
{
    public function __invoke($data, $context = null)
    {
        return false;
    }
}
