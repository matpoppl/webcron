<?php

namespace matpoppl\HttpMessage;

class UriGlobalsFactory
{
    /**
     * 
     * @throws \UnexpectedValueException
     * @return \matpoppl\HttpMessage\Uri
     */
    public function createFromGlobals()
    {
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
        $port = $_SERVER['SERVER_PORT'];
        
        $uri = $scheme . '://';
        
        if (array_key_exists('PHP_AUTH_USER', $_SERVER)) {
            $uri .= $_SERVER['PHP_AUTH_USER'];
            
            if (array_key_exists('PHP_AUTH_PW', $_SERVER)) {
                $uri .= ':' . $_SERVER['PHP_AUTH_PW'];
            }
            
            $uri .= '@';
        }
        
        $uri .= $_SERVER['SERVER_NAME'];
        
        switch ($scheme.$port) {
            case 'http80':
            case 'https443':
                break;
            default:
                $uri .= ':' . $port;
        }
        
        $uri .= $_SERVER['REQUEST_URI'];
        
        if (! filter_var($uri, \FILTER_VALIDATE_URL)) {
            throw new \UnexpectedValueException('Invalid URL format');
        }
        
        return new Uri($uri);
    }
}
