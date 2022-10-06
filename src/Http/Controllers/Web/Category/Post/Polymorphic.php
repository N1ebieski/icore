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

namespace N1ebieski\ICore\Http\Controllers\Web\Category\Post;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\ShowRequest;

interface Polymorphic
{
    /**
     * Display a listing of the Posts for Category.
     *
     * @param  Category $category [description]
     * @param ShowRequest $request
     * @return HttpResponse [description]
     */
    public function show(Category $category, ShowRequest $request): HttpResponse;
}
