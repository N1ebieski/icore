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

namespace N1ebieski\ICore\Attributes\Category;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

class CurrentLang
{
    /**
     *
     * @param Category $category
     * @return void
     */
    public function __construct(protected Category $category)
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
            get: function (): CategoryLang {
                $categoryLang = $this->category->langs->firstWhere('lang', Config::get('app.locale'));

                return $categoryLang ?? $this->category->langs()->make();
            }
        );
    }
}
