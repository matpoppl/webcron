<?php

namespace matpoppl\DataFilter;

class ToIntFilter implements FilterInterface
{
    public function __invoke($data)
    {
        return (int) $data;
    }
}
