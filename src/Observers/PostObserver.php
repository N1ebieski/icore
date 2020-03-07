<?php

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Post;

class PostObserver
{
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
        Cache::tags(['post.'.$post->slug, 'posts'])->flush();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        Cache::tags(['post.'.$post->slug, 'posts'])->flush();
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
