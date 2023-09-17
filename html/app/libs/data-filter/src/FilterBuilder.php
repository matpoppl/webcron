<?php

namespace matpoppl\DataFilter;

use matpoppl\ServiceManager\ServiceManagerInterface;
use matpoppl\ServiceManager\Factory\InvokableFactory;

class FilterBuilder
{
    /** @var ServiceManagerInterface */
    private $sm;
    
    public function __construct(ServiceManagerInterface $sm)
    {
        $sm->addAliases([
            'data.filter.StringTrim' => StringTrimFilter::class,
            'data.filter.Callback' => CallbackFilter::class,
            'data.filter.ToNull' => ToNullFilter::class,
            'data.filter.ToInt' => ToIntFilter::class,
            'data.filter.NumberNormalizer' => NumberNormalizerFilter::class,
        ]);
        
        $sm->addFactories([
            CallbackFilter::class => InvokableFactory::class,
            StringTrimFilter::class => InvokableFactory::class,
            ToNullFilter::class => InvokableFactory::class,
            ToIntFilter::class => InvokableFactory::class,
            NumberNormalizerFilter::class => InvokableFactory::class,
        ]);
        
        $this->sm = $sm;
    }
    
    public function createFilterChain(array $options)
    {
        $options = array_key_exists('filters', $options) ? $options : ['filters' => $options];
        
        foreach ($options['filters'] as $i => $filter) {
            $options['filters'][$i] = $this->createFilter($filter);
        }
        
        return new FilterChain($options);
    }
    
    public function createFilter($options)
    {
        if (is_string($options)) {
            $type = $options;
            $options = null;
        } else if (is_array($options)) {
            $type = $options['type'] ?? null;
            $options = $options['options'] ?? null;
        } else {
            throw new \UnexpectedValueException('Unsupported filter options type');
        }
        
        if ($this->sm->has($type)) {
            return $this->sm->create($type, $options);
        }
        
        $type = 'data.filter.' . $type;
        
        if (! $this->sm->has($type)) {
            throw new \UnexpectedValueException('Validator dont exists `'.$type.'`');
        }
        
        return $this->sm->create($type, $options);
    }
}
