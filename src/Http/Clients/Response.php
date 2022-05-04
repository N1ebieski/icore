<?php

namespace N1ebieski\ICore\Http\Clients;

abstract class Response
{
    /**
     * Undocumented variable
     *
     * @var array|object
     */
    protected $parameters;

    /**
     * Undocumented function
     *
     * @param array|object $parameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;

        $this->setParameters($parameters);
    }

    /**
     * Undocumented function
     *
     * @param array|object $parameters
     * @param array $names
     * @return void
     */
    protected function setParameters($parameters, array $names = [])
    {
        foreach ($parameters as $key => $value) {
            if (!is_array($value) || !$this->isContainsStringKey($value)) {
                $this->set(implode('.', array_merge($names, [$key])), $value);

                continue;
            }

            $this->setParameters($value, array_merge($names, [$key]));
        }
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function set(string $key, $value)
    {
        $names = explode('.', $key);

        $setterName = $this->setterName($names);

        if (method_exists($this, $setterName)) {
            $this->{$setterName}($value);
        }
    }

    /**
     * Undocumented function
     *
     * @return object|array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $names = explode('.', $key);

        $getterName = $this->getterName($names);

        if (method_exists($this, $getterName)) {
            return $this->{$getterName}();
        } else {
            $parameter = $this->parameters;

            foreach ($names as $name) {
                if (is_array($parameter) && isset($parameter[$name])) {
                    $parameter = $parameter[$name];
                } elseif (is_object($parameter) && isset($parameter->{$name})) {
                    $parameter = $parameter->{$name};
                } else {
                    return null;
                }
            }

            return $parameter;
        }
    }

    /**
     * Undocumented function
     *
     * @param array $values
     * @return string
     */
    protected function setterName(array $values): string
    {
        return 'set' . implode('', array_map("ucfirst", $values));
    }

    /**
     * Undocumented function
     *
     * @param array $values
     * @return string
     */
    protected function getterName(array $values): string
    {
        return 'get' . implode('', array_map("ucfirst", $values));
    }

    /**
     * Undocumented function
     *
     * @param array $array
     * @return boolean
     */
    protected function isContainsStringKey(array $array): bool
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
}
