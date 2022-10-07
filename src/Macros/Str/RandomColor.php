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

namespace N1ebieski\ICore\Macros\Str;

use Closure;
use Illuminate\Support\Str;

class RandomColor extends Str
{
    /**
     *
     * @return Closure
     */
    public function __invoke(): Closure
    {
        return function (string $value) {
            $hash = md5('color' . $value);

            $rgb = [
                hexdec(substr($hash, 0, 2)),
                hexdec(substr($hash, 2, 2)),
                hexdec(substr($hash, 4, 2))
            ];

            return 'rgb(' . implode(', ', $rgb) . ')';
        };
    }
}