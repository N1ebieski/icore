<?php

namespace N1ebieski\ICore\Utils\Traits;

trait Factory
{
    /**
     * Inside factory. Two methods of injection:
     * 1. Array - key as parameter name, value as parameter value, example make(['file' => new File(), 'path' => 'test/'])
     * 2. Parameters - counting from the end, earlier injected from existing object properties, example make(new File(), 'test/')
     *
     * @param array ...$parameters
     * @return static
     */
    public function make(...$parameters)
    {
        $class = new \ReflectionClass(static::class);

        $newParameters = [];
        $oldParameters = $class->getConstructor()->getParameters();
        $methodArray = false;

        $y = count($oldParameters) - count($parameters);

        for ($i = 0; $i < count($oldParameters); $i++) {
            if (isset($parameters[0]) && is_array($parameters[0]) && isset($parameters[0][$oldParameters[$i]->name])) {
                $newParameters[] = $parameters[0][$oldParameters[$i]->name];

                $methodArray = true;
            } elseif (array_key_exists($i - $y, $parameters) && $methodArray === false) {
                $newParameters[] = $parameters[$i - $y];
            } elseif (isset($this->{$oldParameters[$i]->name})) {
                $newParameters[] = $this->{$oldParameters[$i]->name};
            } else {
                $newParameters[] = null;
            }
        }

        return new static(...$newParameters);
    }
}
