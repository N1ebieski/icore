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

namespace N1ebieski\ICore\Jobs\AutoTranslate\Data\MailingLang;

use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\OutputDataInterface;

class MailingLangOutputData implements OutputDataInterface
{
    use ConditionallyLoadsAttributes;

    /**
     *
     * @param MailingLang $mailingLang
     * @param Collect $collect
     * @param Pipeline $pipeline
     * @return void
     */
    public function __construct(
        protected MailingLang $mailingLang,
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
