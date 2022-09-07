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

namespace N1ebieski\ICore\Casts\User;

use N1ebieski\ICore\ValueObjects\User\Marketing;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MarketingCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Marketing
     */
    public function get($model, string $key, $value, array $attributes): Marketing
    {
        return (!$value instanceof Marketing) ? new Marketing($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Marketing
     */
    public function set($model, string $key, $value, array $attributes): Marketing
    {
        if (is_string($value)) {
            $value = Marketing::fromString($value);
        }

        if (is_int($value)) {
            $value = new Marketing($value);
        }

        if (!$value instanceof Marketing) {
            throw new \InvalidArgumentException('The given value is not a Marketing instance');
        }

        return $value;
    }
}
