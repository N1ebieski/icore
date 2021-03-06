<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\BanModel;

use N1ebieski\ICore\Models\BanModel\BanModel;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\DestroyGlobalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\Polymorphic;

/**
 * [BanModelController description]
 */
class BanModelController implements Polymorphic
{
    /**
     * Remove the specified BanModel from storage.
     *
     * @param  BanModel         $banModel [description]
     * @return JsonResponse       [description]
     */
    public function destroy(BanModel $banModel) : JsonResponse
    {
        $banModel->delete();

        return response()->json(['success' => '']);
    }

    /**
     * Remove the collection of BanModels from storage.
     *
     * @param  BanModel         $banModel [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(BanModel $banModel, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $banModel->whereIn('id', $request->get('select'))->delete();

        return redirect()->back()->with('success', trans('icore::bans.success.destroy_global', ['affected' => $deleted]));
    }
}
