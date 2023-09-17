<?php

namespace matpoppl\Email\DataFilter;

use matpoppl\DataFilter\FilterInterface;

class EmailAddressDetailsFilter implements FilterInterface
{
    public function __invoke($addresses)
    {
        $ret = [];
        
        foreach ($this->extract($addresses) as $email => $name) {
            if ($name) {
                $ret[] = "\"{$name}\" <$email>";
            } else {
                $ret[] = $email;
            }
        }
        
        return implode(', ', $ret);
    }
    
    public function extract(string $addresses)
    {
        $ret = [];
        
        foreach (explode(',', $addresses) as $address) {
            
            $parts = explode('<', $address, 2);
            
            $name = null;
            $email = null;
            
            switch (count($parts)) {
                case 2:
                    
                    $name = trim(' "\'', $parts[0]);
                    $email = $parts[1];
                    
                    break;
                case 1:
                    
                    $email = $parts[0];
                    
                    break;
                default:
                    throw new \UnexpectedValueException('Unsupported number of parts');
            }
            
            $email = trim(' <>', $email);
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $ret[$email] = $name;
            }
        }
        
        return $ret;
    }
}
