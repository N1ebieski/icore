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

namespace N1ebieski\ICore\Attributes\Page;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ShortContent
{
    /**
     *
     * @param Page $page
     * @return void
     */
    public function __construct(protected Page $page)
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
                return e(mb_substr(strip_tags($this->page->replacement_content), 0, 500), false);
            }
        );
    }
}
