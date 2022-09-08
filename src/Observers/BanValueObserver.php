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

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Support\Facades\Cache;

class BanValueObserver
{
    /**
     * Handle the ban value "created" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function created(BanValue $banValue)
    {
        Cache::tags(['bans.' . $banValue->type->getValue()])->flush();
    }

    /**
     * Handle the ban value "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function updated(BanValue $banValue)
    {
        Cache::tags(['bans.' . $banValue->type->getValue()])->flush();
    }

    /**
     * Handle the ban value "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function deleted(BanValue $banValue)
    {
        Cache::tags(['bans.' . $banValue->type->getValue()])->flush();
    }

    /**
     * Handle the ban value "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function restored(BanValue $banValue)
    {
        //
    }

    /**
     * Handle the ban value "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function forceDeleted(BanValue $banValue)
    {
        //
    }
}
