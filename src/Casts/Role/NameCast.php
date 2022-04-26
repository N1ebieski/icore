<?php

namespace N1ebieski\ICore\Casts\Role;

use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class NameCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Name
     */
    public function get($model, string $key, $value, array $attributes): Name
    {
        return (!$value instanceof Name) ? new Name($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Name
     */
    public function set($model, string $key, $value, array $attributes): Name
    {
        if (is_string($value)) {
            $value = new Name($value);
        }

        if (!$value instanceof Name) {
            throw new \InvalidArgumentException('The given value is not a Name instance');
        }

        return $value;
    }
}
