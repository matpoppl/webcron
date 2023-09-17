<?php

namespace matpoppl\DataFilter;

class FilterChain implements FilterInterface
{
    /** @val FilterInterface[] */
    private $filters;
    
    public function __construct(array $options)
    {
        $this->filters = $options['filters'] ?? [];
    }
    
    public function __invoke($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter($data);
        }
        
        return $data;
    }
}
