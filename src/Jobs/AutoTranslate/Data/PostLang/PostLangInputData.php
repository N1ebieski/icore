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

use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\PostLang\PostLang;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\InputDataInterface;

class PostLangInputData implements InputDataInterface
{
    /**
     *
     * @param PostLang $postLang
     * @param Collect $collect
     * @param Config $config
     * @param Pipeline $pipeline
     * @return void
     */
    public function __construct(
        protected PostLang $postLang,
        protected Collect $collect,
        protected Config $config,
        protected Pipeline $pipeline
    ) {
        //
    }

    /**
     *
     * @return array
     */
    public function getInputToArray(): array
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

    /**
     *
     * @param array $attributes
     * @return array
     */
    public function getOutputToArray(array $attributes): array
    {
        if (array_key_exists('tags', $attributes)) {
            $attributes['tags'] = explode(
                ',',
                !is_null($this->config->get('icore.tag.normalizer')) ?
                    $this->config->get('icore.tag.normalizer')($attributes['tags'])
                    : $attributes['tags']
            );
        }

        if (array_key_exists('content_html', $attributes)) {
            $attributes['content_html'] = !empty($attributes['content_html']) ?
                $this->pipeline->send($attributes['content_html'])
                    ->through([
                        \N1ebieski\ICore\Utils\Conversions\ClearWhitespacesBeforeCode::class
                    ])
                    ->thenReturn()
                : null;
        }

        return $attributes;
    }
}
