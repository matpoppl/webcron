<?php

namespace App\Security;

interface AuthManagerInterface
{
    /**
     * 
     * @param string $username
     * @param string $password
     * @return \matpoppl\SmallMVC\Security\AuthResultInterface
     */
    public function signin(string $username, string $password);
}
