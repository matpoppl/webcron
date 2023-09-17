<?php

namespace matpoppl\HttpMessage\ParsedBody;

use Psr\Http\Message\RequestInterface;

class ParsedBodyGlobalsFactory
{
    public function createFrom(RequestInterface $req)
    {
        $contentType = $req->getHeaderLine('Content-Type');
        
        $split = strpos($contentType, ';');
        
        if ($split > 0) {
            $contentType = substr($contentType, 0, $split);
        }
        
        switch (strtolower($contentType)) {
            case 'multipart/form-data':
            case 'application/x-www-form-urlencoded':
                return new PostData($_POST);
            case 'application/json':
                $body = $req->getBody();
                
                if ($body->getSize() > 0) {
                    return JsonData::fromString(''.$body);
                }
        }
        
        // always return something
        return new PostData([]);
    }
}
