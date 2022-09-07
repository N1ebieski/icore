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

namespace N1ebieski\ICore\Http\Controllers\Admin\BanModel\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\BanModel\User\BanModel;
use N1ebieski\ICore\Filters\Admin\BanModel\User\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\User\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\User\StoreRequest;

interface Polymorphic
{
    /**
     * Display a listing of the BanModel.
     *
     * @param BanModel $banModel
     * @param  IndexRequest $request  [description]
     * @param  IndexFilter  $filter   [description]
     * @return HttpResponse           [description]
     */
    public function index(BanModel $banModel, IndexRequest $request, IndexFilter $filter): HttpResponse;

    /**
     * Show the form for creating a new BanModel.
     *
     * @param  User         $user [description]
     * @return JsonResponse       [description]
     */
    public function create(User $user): JsonResponse;

    /**
     * Store a newly created BanModel and BanValue.ip in storage.
     *
     * @param  User         $user     [description]
     * @param  BanModel     $banModel [description]
     * @param  BanValue     $banValue [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(User $user, BanModel $banModel, BanValue $banValue, StoreRequest $request): JsonResponse;
}
