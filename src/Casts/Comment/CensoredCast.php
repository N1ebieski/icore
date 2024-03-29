<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Casts\Comment;

use N1ebieski\ICore\ValueObjects\Comment\Censored;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CensoredCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Censored
     */
    public function get($model, string $key, $value, array $attributes): Censored
    {
        return (!$value instanceof Censored) ? new Censored($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        if (is_string($value)) {
            $value = Censored::fromString($value);
        }

        if (is_int($value)) {
            $value = new Censored($value);
        }

        if (!$value instanceof Censored) {
            throw new \InvalidArgumentException('The given value is not a Censored instance');
        }

        return $value->getValue();
    }
}
