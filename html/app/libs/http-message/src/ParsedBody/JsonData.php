<?php

namespace matpoppl\HttpMessage\ParsedBody;

class JsonData extends AbstractData
{
    public static function fromString(string $data)
    {
        $json = json_decode($data, true);
        $code = json_last_error();
        
        if (JSON_ERROR_NONE === $code) {
            return new static($json);
        }
        
        throw new \UnexpectedValueException(json_last_error_msg(), $code);
    }
}
