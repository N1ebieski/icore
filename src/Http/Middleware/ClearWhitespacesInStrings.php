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

namespace N1ebieski\ICore\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ClearWhitespacesInStrings extends TransformsRequest
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $except = ['content_html'];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (!in_array($key, $this->except) && is_string($value)) {
            $value = str_replace("&nbsp;", " ", $value);
            $value = preg_replace('/[[:blank:]]+/', ' ', $value);
        }

        return $value;
    }
}
