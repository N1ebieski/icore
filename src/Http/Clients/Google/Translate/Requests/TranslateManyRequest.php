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

namespace N1ebieski\ICore\Http\Clients\Google\Translate\Requests;

use N1ebieski\ICore\Http\Clients\Request;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslateManyRequest extends Request
{
    /**
     *
     * @param array $parameters
     * @param TranslateClient $translateClient
     * @return void
     */
    public function __construct(
        protected array $parameters,
        protected TranslateClient $translateClient
    ) {
        $this->setParameters($parameters);
    }

    /**
     *
     * @return array
     */
    public function makeRequest(): array
    {
        return $this->translateClient->translateBatch($this->get('strings'), [
            'source' => $this->get('source'),
            'target' => $this->get('target')
        ]);
    }
}
