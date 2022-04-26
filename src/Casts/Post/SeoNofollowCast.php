<?php

namespace N1ebieski\ICore\Casts\Post;

use N1ebieski\ICore\ValueObjects\Post\SeoNofollow;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SeoNofollowCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return SeoNofollow
     */
    public function get($model, string $key, $value, array $attributes): SeoNofollow
    {
        return (!$value instanceof SeoNofollow) ? new SeoNofollow($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return SeoNofollow
     */
    public function set($model, string $key, $value, array $attributes): SeoNofollow
    {
        if (is_string($value)) {
            $value = SeoNofollow::fromString($value);
        }

        if (!$value instanceof SeoNofollow) {
            throw new \InvalidArgumentException('The given value is not a SeoNofollow instance');
        }

        return $value;
    }
}
