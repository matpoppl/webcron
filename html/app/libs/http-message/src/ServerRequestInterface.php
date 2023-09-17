<?php

namespace matpoppl\HttpMessage;

interface ServerRequestInterface extends \Psr\Http\Message\ServerRequestInterface
{
    public function has(string $id) : bool;
    public function get(string $id, $default = null);
    public function set(string $id, $value);
    public function remove(string $id);
}
