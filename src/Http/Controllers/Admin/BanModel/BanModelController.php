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

namespace N1ebieski\ICore\Http\Controllers\Admin\BanModel;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Models\BanModel\BanModel;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\Polymorphic;
use N1ebieski\ICore\Http\Requests\Admin\BanModel\DestroyGlobalRequest;

class BanModelController implements Polymorphic
{
    /**
     * Remove the specified BanModel from storage.
     *
     * @param  BanModel         $banModel [description]
     * @return JsonResponse       [description]
     */
    public function destroy(BanModel $banModel): JsonResponse
    {
        $banModel->delete();

        return response()->json([]);
    }

    /**
     * Remove the collection of BanModels from storage.
     *
     * @param  BanModel         $banModel [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(BanModel $banModel, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $banModel->whereIn('id', $request->get('select'))->delete();

        return redirect()->back()->with('success', trans('icore::bans.success.destroy_global', ['affected' => $deleted]));
    }
}
