<?php

namespace matpoppl\DataValidator;

interface ValidatorInterface
{
    /**
     * 
     * @param mixed $data
     * @param mixed $context
     * @return bool|array FALSE if valid or Array of error messages
     */
    public function __invoke($data, $context = null);
}
