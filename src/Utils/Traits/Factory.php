<?php

namespace N1ebieski\ICore\Utils\Traits;

trait Factory
{
    /**
     * Undocumented function
     *
     * @param array ...$parameters
     * @return static
     */
    public function make(...$parameters)
    {
        $class = new \ReflectionClass(static::class);

        $newParameters = [];
        $i = 0;

        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if (isset($parameters[0]) && is_array($parameters[0]) && isset($parameters[0][$parameter->name])) {
                $newParameters[] = $parameters[0][$parameter->name];
            } else if (isset($this->{$parameter->name})) {
                $newParameters[] = $this->{$parameter->name};
            } else {
                $newParameters[] = $parameters[$i];

                $i++;
            }
        }

        return new static(...$newParameters);
    }
}
