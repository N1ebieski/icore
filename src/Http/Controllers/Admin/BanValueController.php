<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Filters\Admin\BanModel\User\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\BanValue\UpdateRequest;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

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
     * @return View                   [description]
     */
    public function index(string $type, BanValue $banValue, IndexRequest $request, IndexFilter $filter) : View
    {
        $bans = $banValue->getRepo()->paginateByFilter(array_merge(['type' => $type], $filter->all()));

        return view('icore::admin.banvalue.index', [
            'type' => $type,
            'bans' => $bans,
            'filter' => $filter->all(),
            'paginate' => config('icore.database.paginate')
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
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.banvalue.edit', [
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

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.banvalue.ban', [
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
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.banvalue.create', [
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

        $request->session()->flash('success', trans('icore::bans.value.success.store'));

        return response()->json(['success' => '' ]);
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

        return response()->json(['success' => '']);
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

        return redirect()->back()->with('success', trans('icore::bans.success.destroy_global', ['affected' => $deleted]));
    }
}
