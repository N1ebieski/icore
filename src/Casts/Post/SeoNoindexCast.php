<?php

namespace N1ebieski\ICore\Casts\Post;

use N1ebieski\ICore\ValueObjects\Post\SeoNoindex;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SeoNoindexCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return SeoNoindex
     */
    public function get($model, string $key, $value, array $attributes): SeoNoindex
    {
        return (!$value instanceof SeoNoindex) ? new SeoNoindex($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return SeoNoindex
     */
    public function set($model, string $key, $value, array $attributes): SeoNoindex
    {
        if (is_string($value)) {
            $value = SeoNoindex::fromString($value);
        }

        if (is_int($value)) {
            $value = new SeoNoindex($value);
        }

        if (!$value instanceof SeoNoindex) {
            throw new \InvalidArgumentException('The given value is not a SeoNoindex instance');
        }

        return $value;
    }
}
