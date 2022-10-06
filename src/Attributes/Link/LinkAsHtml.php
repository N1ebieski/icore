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

namespace N1ebieski\ICore\Attributes\Link;

use N1ebieski\ICore\Models\Link;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LinkAsHtml
{
    public function __construct(protected Link $link)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): string {
                $output = '<a href="' . e($this->link->url) . '" title="' . e($this->link->name) . '">';

                if (!is_null($this->link->img_url)) {
                    $output .= '<img src="' . e($this->link->img_url_from_storage) . '" alt="' . e($this->link->name) . '" class="img-fluid">';
                } else {
                    $output .= e($this->link->name);
                }

                $output .= '</a>';

                return $output;
            }
        );
    }
}
