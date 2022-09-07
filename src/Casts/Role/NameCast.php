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
