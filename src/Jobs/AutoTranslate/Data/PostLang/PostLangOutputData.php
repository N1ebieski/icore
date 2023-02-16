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
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\OutputDataInterface;

class PostLangOutputData implements OutputDataInterface
{
    use ConditionallyLoadsAttributes;

    /**
     *
     * @param PostLang $postLang
     * @param Config $config
     * @param Collect $collect
     * @param Pipeline $pipeline
     * @return void
     */
    public function __construct(
        protected PostLang $postLang,
        protected Config $config,
        protected Collect $collect,
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
        return $this->collect->make($attributes)
            ->merge([
                $this->mergeWhen(
                    array_key_exists('tags', $attributes),
                    function () use ($attributes) {
                        return explode(
                            ',',
                            !is_null($this->config->get('icore.tag.normalizer')) ?
                                $this->config->get('icore.tag.normalizer')($attributes['tags'])
                                : $attributes['tags']
                        );
                    }
                ),
                $this->mergeWhen(
                    array_key_exists('content_html', $attributes),
                    function () use ($attributes) {
                        return !empty($attributes['content_html']) ?
                            $this->pipeline->send($attributes['content_html'])
                                ->through([
                                    \N1ebieski\ICore\Utils\Conversions\ClearWhitespacesBeforeCode::class
                                ])
                                ->thenReturn()
                            : null;
                    }
                )
            ])
            ->transform(fn ($item) => !empty($item) ? $item : null)
            ->toArray();
    }
}
