<?php

namespace App\Security;

use const PHP_INT_MAX;

class RandomSource
{
    public function getBytes($length, $raw = false) : string
    {
        if (! $raw) {
            $length += 50;
        }
        
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length);
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length);
        } else {
            $chars = [];
            for ($i = 0; $i < $length; $i++) {
                $chars[] = $i;
            }
            $bytes = pack('C*', ...$chars);
        }
        
        if ($raw) {
            return $bytes;
        }
        
        return substr(preg_replace('#\W+#', '', base64_encode($bytes)), 0, $length - 50);
    }

    public function getInt($max = null, $min = null) : int
    {
        if (function_exists('random_int')) {
            return random_int($min > 0 ? $min : 0, $max > 0 ? $max : PHP_INT_MAX);
        }
        
        mt_srand();
        return mt_rand($min > 0 ? $min : 0, $max > 0 ? $max : mt_getrandmax());
    }
}
