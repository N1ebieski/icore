<?php

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\Rating\Rating;

/**
 * [RatingObserver description]
 */
class RatingObserver
{
    /**
     * Handle the rating "created" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function created(Rating $rating)
    {
        cache()->tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "updated" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function updated(Rating $rating)
    {
        cache()->tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "deleted" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function deleted(Rating $rating)
    {
        cache()->tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "restored" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function restored(Rating $rating)
    {
        //
    }

    /**
     * Handle the rating "force deleted" event.
     *
     * @param  Rating  $dir
     * @return void
     */
    public function forceDeleted(Rating $dir)
    {
        //
    }
}
