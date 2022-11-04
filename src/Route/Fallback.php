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

namespace N1ebieski\ICore\Route;

use Closure;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class Fallback
{
    /**
     *
     * @return Closure
     */
    public function __invoke(): Closure
    {
        return function () {
            /** @var Pipeline */
            $pipeline = App::make(Pipeline::class);

            $oldUrl = URL::full();

            $newUrl = $pipeline->send($oldUrl)
                ->through([
                    \N1ebieski\ICore\Route\Conversions\MultiLang::class
                ])
                ->thenReturn();

            if ($oldUrl === $newUrl) {
                return App::abort(HttpResponse::HTTP_NOT_FOUND);
            }

            return Response::redirectTo($newUrl, HttpResponse::HTTP_MOVED_PERMANENTLY);
        };
    }
}
