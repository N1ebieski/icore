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

namespace N1ebieski\ICore\Http\Clients\Google\Translate;

use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Http\Clients\Google\Translate\Requests\TranslateManyRequest;
use N1ebieski\ICore\Http\Clients\Google\Translate\Responses\TranslateManyResponse;

class TranslateClient
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     *
     * @param array $parameters
     * @return TranslateManyResponse
     * @throws BindingResolutionException
     */
    public function translateMany(array $parameters): TranslateManyResponse
    {
        /** @var TranslateManyRequest */
        $request = $this->app->make(TranslateManyRequest::class, [
            'parameters' => $parameters
        ]);

        return $this->app->make(TranslateManyResponse::class, [
            'parameters' => ['results' => $request->makeRequest()]
        ]);
    }
}
