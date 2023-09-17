<?php

namespace matpoppl\DataValidator;

use const DNS_MX;
use const FILTER_VALIDATE_EMAIL;
use function filter_var;

class EmailAddressValidator extends HostnameValidator
{
    private $restrictUsername = true;
    private $restrictedUsernames = [
        'root',
        'abuse',
        'admin',
        'administrator',
        'hostmaster',
        'postmaster',
    ];
    
    public function __construct(array $options = null)
    {
        if (null === $options) {
            return;
        }
        
        $this->restrictUsername = $options['restrict_username'] ?? true;
        
        if (isset($options['restricted_usernames'])) {
            $this->restrictUsername = $options['restricted_usernames'];
        }
        
        $checkMX = $options['check_mx'] ?? true;
        $options['dns_record_type'] = $options['dns_record_type'] ?? ($checkMX ? DNS_MX : false);
        
        parent::__construct($options);
    }
    
    public function __invoke($data, $ctx = null)
    {
        if ($data !== filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email address format';
        }
        
        $parts = parse_url('email://' . $data);
        
        if (! is_array($parts)) {
            return ['Invalid email address format'];
        }
        
        $user = $parts['user'];
        $host = $parts['host'];
        
        if ($this->restrictUsername && in_array($user, $this->restrictedUsernames)) {
            return 'Username not allowed';
        }
        
        return parent::__invoke($host, $ctx);
    }
}
