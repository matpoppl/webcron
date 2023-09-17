<?php

namespace matpoppl\ServiceManager;

use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\SmallMVC\AppConfig;

class ServiceManager implements ServiceManagerInterface
{
    /** @var string[] */
    private $aliases = [];
    /** @var string[]|array[] */
    private $factories = [];
    /** @var array */
    private $services = [];

    public function __construct(array $options)
    {
        if (array_key_exists('aliases', $options)) {
            $this->aliases = $options['aliases'];
            unset($options['aliases']);
        }
        if (array_key_exists('factories', $options)) {
            $this->factories = $options['factories'];
            unset($options['factories']);
        }
        if (array_key_exists('services', $options)) {
            $this->services = $options['services'];
            unset($options['services']);
        }
    }

    public function configure(array $config)
    {
        if (isset($config['aliases'])) {
            $this->aliases = array_merge($this->aliases, $config['aliases']);
        }
        if (isset($config['factories'])) {
            $this->factories = array_merge($this->factories, $config['factories']);
        }
        if (isset($config['services'])) {
            $this->services = array_merge($this->services, $config['services']);
        }
    }
    
    public function resolveAlias($id)
    {
        while (array_key_exists($id, $this->aliases)) {
            $id = $this->aliases[$id];
        }
        
        return $id;
    }

    public function has(string $id) : bool
    {
        return array_key_exists($id, $this->aliases) || array_key_exists($id, $this->factories) || array_key_exists($id, $this->services);
    }

    public function get(string $id)
    {
        if (! array_key_exists($id, $this->services)) {
            $this->services[$id] = $this->create($id);
        }

        return $this->services[$id];
    }

    public function create(string $id, ...$args)
    {
        $id = $this->resolveAlias($id);
        $factory = $this->getFactoryFor($id);

        if (empty($args)) {
            $specs = $this->getFactorySpecsFor($id);
            $args = $specs['args'] ?? [];
        }

        return $factory($this, $id, ...$args);
    }
    
    public function set(string $id, $value)
    {
        $id = $this->resolveAlias($id);
        $this->services[$id] = $value;
        return $this;
    }
    
    public function getFactoryFor(string $id)
    {
        $specs = $this->getFactorySpecsFor($id);
        
        if (! array_key_exists('factory', $specs)) {
            throw new \InvalidArgumentException('Missing factory definition for `'.$id.'`');
        }
        
        $factory = $specs['factory'];
        
        if (! is_string($factory)) {
            throw new \InvalidArgumentException('Unsupported factory definition for `'.$id.'`');
        }
        
        if ($this->has($factory)) {
            $factory = $this->get($factory);
        } else if (class_exists($factory)) {
            $factory = new $factory();
        } else {
            throw new \InvalidArgumentException('Factory class dont exists for `'.$id.'`');
        }
        
        if (! ($factory instanceof FactoryInterface)) {
            throw new \InvalidArgumentException('Unsupported factory type for `'.$id.'`');
        }
        
        return $factory;
    }
    
    public function getFactorySpecsFor(string $id) : array
    {
        $id = $this->resolveAlias($id);
        
        if (! array_key_exists($id, $this->factories)) {
            throw new \InvalidArgumentException('Factory specs not found `'.$id.'`');
        }
        
        $specs = $this->factories[$id];
        
        return is_array($specs) ? $specs : ['factory' => $specs, 'args' => []];
    }
    
    public function addAliases(array $aliases)
    {
        $this->aliases += $aliases;
        return $this;
    }
    
    public function addFactories(array $factories)
    {
        $this->factories += $factories;
        return $this;
    }
}
