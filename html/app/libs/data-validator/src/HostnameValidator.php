<?php

namespace matpoppl\DataValidator;

use const DNS_A;
use const DNS_PTR;
use const FILTER_VALIDATE_DOMAIN;
use const FILTER_VALIDATE_EMAIL;
use function filter_var;
use function dns_get_record;

class HostnameValidator implements ValidatorInterface
{
    private $recordType = DNS_A;
    private $allowIP = false;
    
    public function __construct(array $options = null)
    {
        if (null === $options) {
            return;
        }
        
        $this->recordType = $options['dns_record_type'] ?? DNS_A;
        $this->allowIP = $options['allow_ip'] ?? false;
    }
    
    public function __invoke($hostname, $ctx = null)
    {
        $isIPLike = preg_match('#^(\d{1,3}\.){3}\d{1,3}$#', $hostname) > 0;
        
        if ($isIPLike) {
            $isIPInvalid = ! filter_var($hostname, FILTER_VALIDATE_IP);
            
            if ($isIPInvalid) {
                return 'Invalid IP format';
            }
            
            if (! $this->allowIP) {
                return 'IP address not allowed';
            }
            
            if (DNS_PTR === $this->recordType) {
                $rev = implode('.', array_reverse(explode('.', $hostname)));;
                $record = dns_get_record($rev . '.in-addr.arpa.', DNS_PTR);
                
                if (false === $record) {
                    return 'Missing DNS record';
                }
            }
            
            return false;
        }
        
        if (! filter_var($hostname, FILTER_VALIDATE_DOMAIN)) {
            return 'Invalid hostname format';
        }
        
        if (! $this->recordType) {
            return false;
        }
        
        if (! $hostname) {
            throw new \UnexpectedValueException('Hostname required');
        }
        
        $record = dns_get_record($hostname, $this->recordType);
        
        if (empty($record)) {
            return 'Missing DNS record';
        }
        
        return false;
    }
}
