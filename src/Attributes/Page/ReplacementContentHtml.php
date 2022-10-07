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

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReplacementContentHtml
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
                return App::make(Pipeline::class)
                    ->send($this->page->content_html ?? '')
                    ->through([
                        \N1ebieski\ICore\Utils\Conversions\Lightbox::class,
                        \N1ebieski\ICore\Utils\Conversions\Replacement::class
                    ])
                    ->thenReturn();
            }
        );
    }
}