<?php

namespace matpoppl\HttpClient;

interface ClientInterface extends \Psr\Http\Client\ClientInterface
{
    public function withOptions(array $options, $merge = false) : ClientInterface;
}
