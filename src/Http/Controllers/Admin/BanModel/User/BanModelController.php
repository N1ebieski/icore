<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\BanModel\User;

use N1ebieski\ICore\Filters\Admin\BanModel\User\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\User\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\User\StoreRequest;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\BanModel\User\BanModel;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\User\Polymorphic as UserPolymorphic;

/**
 * [BanModelController description]
 */
class BanModelController implements UserPolymorphic
{
    /**
     * Display a listing of the BanModel.
     *
     * @param BanModel $banModel
     * @param  IndexRequest $request  [description]
     * @param  IndexFilter  $filter   [description]
     * @return View                 [description]
     */
    public function index(BanModel $banModel, IndexRequest $request, IndexFilter $filter) : View
    {
        $bans = $banModel->paginateByFilter($filter->all());

        return view('icore::admin.banmodel.user.index', [
            'bans' => $bans,
            'filter' => $filter->all(),
            'paginate' => config('icore.database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new BanModel.
     *
     * @param  User         $user [description]
     * @return JsonResponse       [description]
     */
    public function create(User $user) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.banmodel.user.create', [
                'model' => $user
            ])->render()
        ]);
    }

    /**
     * Store a newly created BanModel and BanValue.ip in storage.
     *
     * @param  User         $user     [description]
     * @param  BanModel     $banModel [description]
     * @param  BanValue     $banValue [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(User $user, BanModel $banModel, BanValue $banValue, StoreRequest $request) : JsonResponse
    {
        if ($request->has('user')) {
            $banModel->morph()->associate($user)->save();
        }

        if ($request->has('ip')) {
            $banValue->create([
                'type' => 'ip',
                'value' => $request->input('ip')
            ]);
        }

        return response()->json([
            'success' => trans('icore::bans.model.success.store'),
        ]);
    }
}
