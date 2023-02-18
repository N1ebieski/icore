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

namespace N1ebieski\ICore\Http\Controllers\Admin\CategoryLang;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Http\Requests\Admin\CategoryLang\DestroyRequest;

class CategoryLangController
{
    /**
     *
     * @param CategoryLang $categoryLang
     * @param DestroyRequest $request
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function destroy(CategoryLang $categoryLang, DestroyRequest $request): JsonResponse
    {
        $categoryLang->makeService()->delete();

        return Response::json([], HttpResponse::HTTP_NO_CONTENT);
    }
}
