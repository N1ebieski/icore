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

use Illuminate\Contracts\Pipeline\Pipeline;
use N1ebieski\ICore\Models\PageLang\PageLang;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\OutputDataInterface;

class PageLangOutputData implements OutputDataInterface
{
    /**
     *
     * @param PageLang $pageLang
     * @param Config $config
     * @param Pipeline $pipeline
     * @return void
     */
    public function __construct(
        protected PageLang $pageLang,
        protected Config $config,
        protected Pipeline $pipeline
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return array
     */
    public function getOutput(array $attributes): array
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
