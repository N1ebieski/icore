<?php

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\BanValue;

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
        cache()->tags(['bans.'.$banValue->type])->flush();
    }

    /**
     * Handle the ban value "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function updated(BanValue $banValue)
    {
        cache()->tags(['bans.'.$banValue->type])->flush();
    }

    /**
     * Handle the ban value "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\BanValue  $banValue
     * @return void
     */
    public function deleted(BanValue $banValue)
    {
        cache()->tags(['bans.'.$banValue->type])->flush();
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
