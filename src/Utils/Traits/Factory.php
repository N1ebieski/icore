<?php

namespace N1ebieski\ICore\Utils\Traits;

trait Factory
{
    /**
     * Undocumented function
     *
     * @param array ...$parameters
     * @return self
     */
    public function make(array $parameters)
    {
        $class = new \ReflectionClass(static::class);

        $newParameters = [];

        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $newParameters[] = $parameters[$parameter->name] ?? $this->{$parameter->name};
        }

        return new static(...$newParameters);
    }
}
