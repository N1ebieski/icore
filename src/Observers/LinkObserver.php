<?php

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Link;

/**
 * [LinkObserver description]
 */
class LinkObserver
{
    /**
     * Handle the page "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function created(Link $link)
    {
        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the page "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function updated(Link $link)
    {
        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function deleted(Link $link)
    {
        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the page "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function restored(Link $link)
    {
        //
    }

    /**
     * Handle the page "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function forceDeleted(Link $link)
    {
        //
    }
}
