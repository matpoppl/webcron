<?php

namespace matpoppl\SecurityAcl;

use PHPUnit\Framework\TestCase;

class AclManagerTest extends TestCase
{
    public function testACL()
    {
        $acl = new AclManager();
        
        $acl->addResource('/');
        $acl->addResource('/config');
        $acl->addResource('/signin');
        
        $acl->addRole('guest');
        $acl->addRole('user');
        $acl->addRole('admin', ['user']);
        
        $acl->allow('/config', 'admin', ['read', 'write']);
        $acl->allow('/', 'user', ['read', 'write']);
        $acl->allow('/signin', 'guest', ['read', 'write']);
        
        self::assertFalse($acl->check('/config', 'guest', 'read'));
        self::assertFalse($acl->check('/config', 'user', 'read'));
        self::assertTrue($acl->check('/config', 'admin', 'read'));
        self::assertFalse($acl->check('/config', 'guest', 'write'));
        self::assertFalse($acl->check('/config', 'user', 'write'));
        self::assertTrue($acl->check('/config', 'admin', 'write'));
        
        self::assertFalse($acl->check('/', 'guest', 'read'));
        self::assertTrue($acl->check('/', 'user', 'read'));
        self::assertTrue($acl->check('/', 'admin', 'read'));
        
        self::assertTrue($acl->check('/signin', 'guest', 'read'));
        self::assertFalse($acl->check('/signin', 'user', 'read'));
        self::assertFalse($acl->check('/signin', 'admin', 'read'));
    }
}
