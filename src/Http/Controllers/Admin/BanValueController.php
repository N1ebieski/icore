<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\BanModel\User\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\DestroyGlobalRequest;

/**
 * [BanValueController description]
 */
class BanValueController
{
    /**
     * Display a listing of the BanValue.
     *
     * @param  string       $type     [description]
     * @param  BanValue     $banValue [description]
     * @param  IndexRequest $request  [description]
     * @param  IndexFilter  $filter   [description]
     * @return HttpResponse           [description]
     */
    public function index(string $type, BanValue $banValue, IndexRequest $request, IndexFilter $filter) : HttpResponse
    {
        return Response::view('icore::admin.banvalue.index', [
            'type' => $type,
            'bans' => $banValue->makeRepo()->paginateByFilter($filter->all() + [
                'type' => $type,
                'except' => $request->input('except')
            ]),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Show the form for editing the specified BanValue.
     *
     * @param  BanValue     $banValue [description]
     * @return JsonResponse           [description]
     */
    public function edit(BanValue $banValue) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.banvalue.edit', [
                'ban' => $banValue,
            ])->render()
        ]);
    }

    /**
     * Update the specified BanValue in storage.
     *
     * @param  BanValue      $banValue [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse            [description]
     */
    public function update(BanValue $banValue, UpdateRequest $request) : JsonResponse
    {
        $banValue->update($request->only(['value']));

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.banvalue.partials.ban', [
                'ban' => $banValue,
            ])->render()
        ]);
    }

    /**
     * Show the form for creating a new BanValue.
     *
     * @param  string       $type [description]
     * @param CreateRequest $request
     * @return JsonResponse       [description]
     */
    public function create(string $type, CreateRequest $request) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.banvalue.create', [
                'type' => $type,
            ])->render()
        ]);
    }

    /**
     * Store a newly created BanValue in storage.
     *
     * @param  string       $type     [description]
     * @param  BanValue     $banValue [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(string $type, BanValue $banValue, StoreRequest $request) : JsonResponse
    {
        $banValue->create([
            'type' => $type,
            'value' => $request->get('value')
        ]);

        $request->session()->flash('success', Lang::get('icore::bans.value.success.store'));

        return Response::json(['success' => '' ]);
    }

    /**
     * Remove the specified BanValue from storage.
     *
     * @param  BanValue         $banValue [description]
     * @return JsonResponse       [description]
     */
    public function destroy(BanValue $banValue) : JsonResponse
    {
        $banValue->delete();

        return Response::json(['success' => '']);
    }

    /**
     * Remove the collection of BanValues from storage.
     *
     * @param  BanValue         $banValue [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(BanValue $banValue, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $banValue->whereIn('id', $request->get('select'))->delete();

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::bans.success.destroy_global', ['affected' => $deleted])
        );
    }
}
