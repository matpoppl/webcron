<?php

namespace matpoppl\SmallMVC\Router\Route;

use Psr\Http\Message\RequestInterface;
use matpoppl\SmallMVC\Router\MatchInterface;
use matpoppl\SmallMVC\Router\MatchResult;

class SegmentRoute extends AbstractRoute
{
    private $path;
    private $pattern;
    private $constraints;

    public function __construct(array $options)
    {
        $path = $options['path'] ?? '';
        $this->path = $path;
        
        $this->constraints = $options['constraints'] ?? [];
        
        foreach ($this->constraints as $name => $pattern) {
            $path = str_replace('{'.$name.'}', "(?<$name>$pattern)", $path);
        }
        
        $this->pattern = $path;
        
        parent::__construct($options);
    }

    public function hasMatch(RequestInterface $request) : ?MatchInterface
    {
        $path = $request->getUri()->getPath();

        $matched = null;
        if (preg_match('#^'.$this->pattern.'#', $path, $matched) < 1) {
            return null;
        }

        return new MatchResult(array_intersect_key($matched, $this->constraints) + $this->getDefaults());
    }
    
    public function buildPath(array $params = null): string
    {
        $path = $this->path;
        
        if (null === $params) {
            $params = $this->getDefaults();
        } else {
            $params += $this->getDefaults();
        }
        
        foreach (array_keys($this->constraints) as $name) {
            if (! array_key_exists($name, $params)) {
                throw new \DomainException('Missing route parametr `'.$name.'`');
            }
            
            $param = (string) $params[$name];
            
            if (preg_match('#^'.$this->constraints[$name].'$#', $param) < 1) {
                throw new \UnexpectedValueException('Unsupported route parameter type `'.$name.'`');
            }
            
            $path = str_replace('{'.$name.'}', $param, $path);
        }
        
        return $path;
    }

}
