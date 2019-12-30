<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\BanModel;

use N1ebieski\ICore\Models\BanModel\BanModel;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\DestroyGlobalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Remove the specified BanModel from storage.
     *
     * @param  BanModel         $banModel [description]
     * @return JsonResponse       [description]
     */
    public function destroy(BanModel $banModel) : JsonResponse;

    /**
     * Remove the collection of BanModels from storage.
     *
     * @param  BanModel         $banModel [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(BanModel $banModel, DestroyGlobalRequest $request) : RedirectResponse;
}
