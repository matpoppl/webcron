<?php

namespace matpoppl\Mailer;

class Random
{
    /**
     * 
     * @param int $length
     * @return string
     */
    public static function createId(int $length) : string
    {
        static $created = [];
        
        do {
            $rnd = preg_replace('#\W+#', '', base64_encode(random_bytes($length + $length / 2)));
        } while(array_key_exists($rnd, $created));
        
        $created[$rnd] = true;
        
        return substr($rnd, 0, $length);
    }
}
