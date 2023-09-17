<?php

namespace matpoppl\DataFilter;

interface FilterInterface
{
    public function __invoke($data);
}
