<?php

namespace matpoppl\HttpMessage;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\HttpMessage\ParsedBody\ParsedBodyGlobalsFactory;

class ServerRequestGlobalsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $headers = Headers::createFromGlobals();
        $body = InMemoryStream::fromResource(fopen('php://input', 'r'));
        
        $ver = substr($_SERVER['SERVER_PROTOCOL'] ?? '', 5);
        
        $uriFactory = new UriGlobalsFactory();
        
        /** @var ServerRequest $req */
        $req = new $name($headers, $body, $ver ?: null);

        $req = $req->withUri($uriFactory->createFromGlobals())
            ->withMethod(strtoupper($_SERVER['REQUEST_METHOD'] ?? null))
            ->withCookieParams($_COOKIE)
            ->withProtocolVersion($_SERVER['SERVER_PROTOCOL']);
            
        if ($req instanceof ServerRequest) {
            $req->setParsedBodyFactory(new ParsedBodyGlobalsFactory());
        }

        return $req;
    }
}
