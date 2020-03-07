<?php

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Page\Page;

/**
 * [PageObserver description]
 */
class PageObserver
{
    /**
     * Handle the page "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function created(Page $page)
    {
        Cache::tags(['pages'])->flush();
    }

    /**
     * Handle the page "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function updated(Page $page)
    {
        Cache::tags(['page.'.$page->slug, 'pages'])->flush();
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function deleted(Page $page)
    {
        Cache::tags(['page.'.$page->slug, 'pages'])->flush();
    }

    /**
     * Handle the page "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function restored(Page $page)
    {
        //
    }

    /**
     * Handle the page "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function forceDeleted(Page $page)
    {
        //
    }
}
