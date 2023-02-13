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

namespace N1ebieski\ICore\Http\Clients\Google\Translate\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use Illuminate\Contracts\Pipeline\Pipeline;

class TranslateManyResponse extends Response
{
    /**
     * Undocumented function
     *
     * @param array|object $parameters
     */
    public function __construct(
        protected array|object $parameters,
        protected Pipeline $pipeline
    ) {
        parent::__construct($parameters);
    }

    /**
     *
     * @param array $results
     * @return TranslateManyResponse
     */
    public function setResults(array $results): self
    {
        foreach ($results as $key => $value) {
            $this->parameters['results'][$key]['text'] = !empty($value['text']) ?
                $this->pipeline->send($value['text'])
                    ->through([
                        \N1ebieski\ICore\Utils\Conversions\ClearWhitespacesBeforeCode::class
                    ])
                    ->thenReturn()
                : null;
        }

        return $this;
    }
}
