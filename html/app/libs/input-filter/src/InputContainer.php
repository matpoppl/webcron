<?php
namespace matpoppl\InputFilter;

use matpoppl\ServiceManager\ServiceManagerInterface;

class InputContainer
{
    /** @var InputInterface[] */
    private $inputs = [];
    
    /** @var InputFactory */
    private $factory;
    
    public function __construct(ServiceManagerInterface $sm, array $inputs)
    {
        $this->inputs = $inputs;
        $this->factory = new InputFactory($sm);
    }
    
    /** @return string[] */
    public function getNames()
    {
        return array_keys($this->inputs);
    }
    
    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->inputs);
    }

    /**
     * 
     * @param string $name
     * @throws \DomainException
     * @throws \UnexpectedValueException
     * @return InputInterface
     */
    public function get(string $name) : InputInterface
    {
        if (! $this->has($name)) {
            throw new \DomainException('Input dont exists `' . $name . '`');
        }

        if (is_array($this->inputs[$name])) {
            $this->inputs[$name] = $this->factory->create($this->inputs[$name]);
        }

        if (! ($this->inputs[$name] instanceof InputInterface)) {
            throw new \UnexpectedValueException('Unsupported input type');
        }

        return $this->inputs[$name];
    }

    /**
     * 
     * @param string $name
     * @param array|InputInterface $input
     * @return \matpoppl\InputFilter\InputContainer
     */
    public function set(string $name, $input)
    {
        $this->inputs[$name] = $input;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \matpoppl\InputFilter\InputContainer
     */
    public function remove(string $name)
    {
        if ($this->has($name)) {
            unset($this->inputs[$name]);
        }
        return $this;
    }
}
