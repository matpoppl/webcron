<?php

namespace matpoppl\Mailer\Transport;

class TransportFactory
{
    public function create(array $options)
    {
        $type = ucfirst($options['type'] ?? '');
        
        $className = __NAMESPACE__ . "\\{$type}Transport";
        
        if (! class_exists($className)) {
            throw new \UnexpectedValueException("Mailer transport type dont exists `{$type}`");
        }
        
        if (! is_subclass_of($className, TransportInterface::class)) {
            throw new \UnexpectedValueException("Unsupported mailer transport `{$type}`");
        }
        
        if (isset($options['options'])) {
            $options = $options['options'];
        } else {
            unset($options);
        }
        
        return new $className($options);
    }
}
