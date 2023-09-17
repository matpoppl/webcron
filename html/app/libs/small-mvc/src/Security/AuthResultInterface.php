<?php

namespace matpoppl\SmallMVC\Security;

const CODE_OK = 0;
const CODE_USER_DONT_EXISTS = 1;
const CODE_AUTH_FAILED = 2;

interface AuthResultInterface
{
    /** @return bool */
    public function hasIdentity();
    /** @return mixed  */
    public function getIdentity();
    /** @return int */
    public function getCode();
    /** @return string */
    public function getMessage();
}
