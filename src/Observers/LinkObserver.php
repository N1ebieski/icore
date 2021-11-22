<?php

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver
{
    /**
     * Handle the link "saving" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function saving(Link $link)
    {
        $link->position = $link->position ?? $link->getNextAfterLastPosition();
    }

    /**
     * Handle the link "saved" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function saved(Link $link)
    {
        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        $link->reorderSiblings();
    }

    /**
     * Handle the link "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function created(Link $link)
    {
        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the link "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function updated(Link $link)
    {
        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the link "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function deleted(Link $link)
    {
        // Everytime the model is removed, we have to decrement siblings position by 1
        $link->decrementSiblings($link->position, null);

        Cache::tags(['links'])->flush();
    }

    /**
     * Handle the link "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function restored(Link $link)
    {
        //
    }

    /**
     * Handle the link "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Link  $link
     * @return void
     */
    public function forceDeleted(Link $link)
    {
        //
    }
}
