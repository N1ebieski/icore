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

namespace N1ebieski\ICore\Jobs\AutoTranslate\Data\PageLang;

use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\PageLang\PageLang;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\InputDataInterface;

class PageLangInputData implements InputDataInterface
{
    /**
     *
     * @param PageLang $pageLang
     * @param Collect $collect
     * @return void
     */
    public function __construct(
        protected PageLang $pageLang,
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
            'title' => $this->pageLang->title,
            'content_html' => $this->pageLang->content_html,
            'seo_title' => $this->pageLang->seo_title,
            'seo_desc' => $this->pageLang->seo_desc,
            'tags' => $this->pageLang->page->tags
                ->where('lang', $this->pageLang->lang)
                ->pluck('name')
                ->implode(', ')
        ])
        ->transform(fn ($item) => $item ?? '')
        ->toArray();
    }
}
