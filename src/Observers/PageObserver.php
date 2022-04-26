<?php

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Page\Page;

class PageObserver
{
    /**
     * [private description]
     * @var bool
     */
    private static $pivotEvent = false;

    /**
     * Handle the link "saving" event.
     *
     * @param  Page  $page
     * @return void
     */
    public function saving(Page $page)
    {
        $page->real_depth = $page->getNextRealDepth();
    }

    /**
     * Handle the link "saved" event.
     *
     * @param  Page  $page
     * @return void
     */
    public function saved(Page $page)
    {
        $page->reorderRealDepths();
    }

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
        Cache::tags(['page.' . $page->slug, 'pages'])->flush();
    }

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @param [type] $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(Page $page, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['tags'])) {
            $this->updated($page);

            static::$pivotEvent = true;
        }
    }

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @return void
     */
    public function pivotDetached(Page $page, $relationName, $pivotIds)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['tags'])) {
            $this->updated($page);

            static::$pivotEvent = true;
        }
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function deleted(Page $page)
    {
        $page->nextSiblings()->decrement($page->getPositionColumn());

        Cache::tags(['page.' . $page->slug, 'pages'])->flush();
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
