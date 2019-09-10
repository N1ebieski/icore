<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Filters\Admin\Mailing\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\DestroyGlobalRequest;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

/**
 * [MailingController description]
 */
class MailingController
{
    /**
     * Display a listing of the Mailing.
     *
     * @param  Mailing         $mailing         [description]
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return View                             [description]
     */
    public function index(Mailing $mailing, IndexRequest $request, IndexFilter $filter) : View
    {
        $mailings = $mailing->getRepo()->paginateByFilter($filter->all());

        return view('icore::admin.mailing.index', [
            'mailings' => $mailings,
            'filter' => $filter->all(),
            'paginate' => config('icore.database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Mailing.
     *
     * @return View               [description]
     */
    public function create() : View
    {
        return view('icore::admin.mailing.create');
    }

    /**
     * Store a newly created Mailing in storage.
     *
     * @param  Mailing          $mailing
     * @param  StoreRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function store(Mailing $mailing, StoreRequest $request) : RedirectResponse
    {
        $mailing = $mailing->getService()->create($request->all());

        return redirect()->route('admin.mailing.index')
            ->with('success', trans('icore::mailings.success.store', ['recipients' => $mailing->total]) );
    }

    /**
     * Show the full-form for editing the specified Mailing.
     *
     * @param Mailing $mailing
     * @return View               [description]
     */
    public function edit(Mailing $mailing) : View
    {
        return view('icore::admin.mailing.edit', ['mailing' => $mailing]);
    }

    /**
     * Update the specified Mailing in storage.
     *
     * @param  Mailing          $mailing    [description]
     * @param  UpdateRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function update(Mailing $mailing, UpdateRequest $request) : RedirectResponse
    {
        $mailing->getService()->update($request->all());

        if ($mailing->status == 1) {
            return redirect()->route('admin.mailing.index')
                ->with('success', trans('icore::mailings.success.update'));
        }

        return redirect()->route('admin.mailing.edit', ['mailing' => $mailing->id])
            ->with('success', trans('icore::mailings.success.update') );
    }

    /**
     * Update Status attribute the specified Mailing in storage.
     *
     * @param  Mailing             $mailing    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Mailing $mailing, UpdateStatusRequest $request) : JsonResponse
    {
        $mailing->getService()->updateStatus($request->only('status'));

        return response()->json([
            'success' => '',
            'status' => $mailing->status,
            'view' => view('icore::admin.mailing.mailing', [
                'mailing' => $mailing->load('emails')
            ])->render(),
        ]);
    }

    /**
     * Reset Recipients the specified Mailing from storage.
     *
     * @param  Mailing         $mailing [description]
     * @return JsonResponse       [description]
     */
    public function reset(Mailing $mailing) : JsonResponse
    {
        $mailing->getService()->reset();

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.mailing.mailing', ['mailing' => $mailing])->render(),
        ]);
    }

    /**
     * Remove the specified Mailing from storage.
     *
     * @param  Mailing $mailing [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Mailing $mailing) : JsonResponse
    {
        $mailing->getService()->delete();

        return response()->json(['success' => '']);
    }

    /**
     * Remove the collection of Mailings from storage.
     *
     * @param  Mailing           $mailing    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Mailing $mailing, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $mailing->getService()->deleteGlobal($request->get('select'));

        return redirect()->back()->with('success', trans('icore::mailings.success.destroy_global', ['affected' => $deleted]));
    }
}
