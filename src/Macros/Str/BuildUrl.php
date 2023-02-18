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

class BuildUrl extends Str
{
    /**
     *
     * @return Closure
     */
    public function __invoke(): Closure
    {
        return function (array $parts) {
            $scheme   = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';

            $host     = $parts['host'] ?? '';
            $port     = isset($parts['port']) ? (':' . $parts['port']) : '';

            $user     = $parts['user'] ?? '';
            $pass     = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
            $pass     = ($user || $pass) ? ($pass . '@') : '';

            $path     = $parts['path'] ?? '';

            $query    = empty($parts['query']) ? '' : ('?' . $parts['query']);

            $fragment = empty($parts['fragment']) ? '' : ('#' . $parts['fragment']);

            return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
        };
    }
}
