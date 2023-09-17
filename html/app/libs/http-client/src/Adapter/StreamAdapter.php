<?php

namespace matpoppl\HttpClient\Adapter;

use Psr\Http\Message\RequestInterface;
use matpoppl\HttpMessage\Response;
use matpoppl\HttpClient\ErrorListener;

class StreamAdapter
{
    private $options = [
        'http' => [
            'ignore_errors' => true,
            'timeout' => 10,
            'follow_location' => true,
            'max_redirects' => 20,
            'protocol_version' => '1.1',
        ],
        /*
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ],
        */
    ];
    
    public function __construct(array $options = null)
    {
        $this->options = $options ?: [];
    }
    
    public function getSSLOptions()
    {
        $opts = array_intersect_key($this->options, [
            'verify_peer' => true,
            'verify_peer_name' => true,
            //'allow_self_signed' => true,
            'cafile' => true,
            'capath' => true,
        ]);
        
        return empty($opts) ? null : $opts;
    }
    
    public function sendRequest(RequestInterface $req)
    {
        $headers = '';
        foreach (array_keys($req->getHeaders()) as $name) {
            $headers .= $req->getHeaderLine($name) . "\r\n";
        }
        
        $body = $req->getBody();
        
        $ctxOpts = array_filter([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 10,
                'follow_location' => true,
                'max_redirects' => 20,
                'protocol_version' => '1.1',
            ],
            'ssl' => $this->getSSLOptions(),
        ]);
        
        $ctxOpts['http']['method'] = $req->getMethod();
        $ctxOpts['http']['header'] = $headers;
        $ctxOpts['http']['content'] = $body ? $body->getContents() : null;
        
        $ctx = stream_context_create($ctxOpts);
        
        // magic variable auto populated by HTTP wrapper
        $http_response_header = null;

        $errors = new ErrorListener();
        
        $resStr = file_get_contents((string) $req->getUri(), false, $ctx);
        
        $errors->stop(false === $resStr);
        
        return Response::fromString((is_array($http_response_header) ? implode("\r\n", $http_response_header) . "\r\n\r\n" : '') . $resStr);
    }
}
