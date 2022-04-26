<?php

namespace N1ebieski\ICore\Casts\Page;

use N1ebieski\ICore\ValueObjects\Page\Comment;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CommentCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Comment
     */
    public function get($model, string $key, $value, array $attributes): Comment
    {
        return (!$value instanceof Comment) ? new Comment($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Comment
     */
    public function set($model, string $key, $value, array $attributes): Comment
    {
        if (is_string($value)) {
            $value = Comment::fromString($value);
        }

        if (!$value instanceof Comment) {
            throw new \InvalidArgumentException('The given value is not a Comment instance');
        }

        return $value;
    }
}
