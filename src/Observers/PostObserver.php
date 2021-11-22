<?php

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    /**
     * [private description]
     * @var bool
     */
    private static $pivotEvent = false;

    /**
     * Handle the post "created" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function created(Post $post)
    {
        Cache::tags(['posts'])->flush();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function updated(Post $post)
    {
        Cache::tags(['post.' . $post->slug, 'posts'])->flush();
    }

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @param [type] $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(Post $post, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
            $this->updated($post);

            static::$pivotEvent = true;
        }
    }

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @return void
     */
    public function pivotDetached(Post $post, $relationName, $pivotIds)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
            $this->updated($post);

            static::$pivotEvent = true;
        }
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        Cache::tags(['post.' . $post->slug, 'posts'])->flush();
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function restored(Post $post)
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function forceDeleted(Post $post)
    {
        //
    }
}
