<?php

namespace matpoppl\DataValidator;

use PHPUnit\Framework\TestCase;

class HostnameValidatorTest extends TestCase
{
    public function testFormat()
    {
        $validator = new HostnameValidator([
            'dns_record_type' => false,
            'allow_ip' => false,
        ]);
        
        self::assertTrue(false === $validator('example.org'), 'Expecting valid domain `example.org`');
        //self::assertTrue(false !== $validator('qwe.asd'), 'Expecting invalid domain `qwe.asd`');
        self::assertTrue(false !== $validator('0.0.0.0'), 'Expecting invalid IP `0.0.0.0`');
        self::assertTrue(false !== $validator('999.999.999.999'), 'Expecting invalid IP `999.999.999.999`');
    }
}
