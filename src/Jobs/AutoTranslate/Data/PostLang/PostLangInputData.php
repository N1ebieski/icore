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

namespace N1ebieski\ICore\Jobs\AutoTranslate\Data\PostLang;

use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\PostLang\PostLang;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\InputDataInterface;

class PostLangInputData implements InputDataInterface
{
    /**
     *
     * @param PostLang $postLang
     * @param Collect $collect
     * @return void
     */
    public function __construct(
        protected PostLang $postLang,
        protected Collect $collect
    ) {
        //
    }

    /**
     *
     * @return array
     */
    public function getInput(): array
    {
        return $this->collect->make([
            'title' => $this->postLang->title,
            'content_html' => $this->postLang->content_html,
            'seo_title' => $this->postLang->seo_title,
            'seo_desc' => $this->postLang->seo_desc,
            'tags' => $this->postLang->post->tags
                ->where('lang', $this->postLang->lang)
                ->pluck('name')
                ->implode(', ')
        ])
        ->transform(fn ($item) => $item ?? '')
        ->toArray();
    }
}
