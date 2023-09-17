<?php

namespace matpoppl\DataFilter;

class NumberNormalizerFilter implements FilterInterface
{
    public function __invoke($data)
    {
        $data = str_replace([' ', ','], ['', '.'], $data);
        $parts = explode('.', $data);
        if (count($parts)<2) {
            return $data;
        }
        
        $dec = array_shift($parts);
        return implode('', $parts) . '.' . $dec;
    }
}
