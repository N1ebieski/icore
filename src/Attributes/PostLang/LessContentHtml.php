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

namespace N1ebieski\ICore\Attributes\PostLang;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LessContentHtml
{
    /**
     *
     * @param PostLang $postLang
     * @return void
     */
    public function __construct(protected PostLang $postLang)
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
                $cut = preg_split('/<p>.*?\[more\].*?<\/p>/', $this->postLang->replacement_content_html);

                // @phpstan-ignore-next-line
                return (!empty($cut[1])) ? $cut[0] . '<p><a href="' . URL::route('web.post.show', [
                    $this->postLang->slug,
                    '#more'
                ]) . '" class="more">' . Lang::get('icore::posts.more') . '</a></p>' : $this->postLang->replacement_content_html;
            }
        );
    }
}
