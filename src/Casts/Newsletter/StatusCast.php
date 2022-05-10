<?php

namespace N1ebieski\ICore\Casts\Newsletter;

use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class StatusCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Status
     */
    public function get($model, string $key, $value, array $attributes): Status
    {
        return (!$value instanceof Status) ? new Status($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Status
     */
    public function set($model, string $key, $value, array $attributes): Status
    {
        if (is_string($value)) {
            $value = Status::fromString($value);
        }

        if (is_int($value)) {
            $value = new Status($value);
        }

        if (!$value instanceof Status) {
            throw new \InvalidArgumentException('The given value is not a Status instance');
        }

        return $value;
    }
}