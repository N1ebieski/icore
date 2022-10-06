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
     *
     * @param Post $post
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @param mixed $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(Post $post, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
            $this->updated($post);

            self::$pivotEvent = true;
        }
    }

    /**
     *
     * @param Post $post
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @return void
     */
    public function pivotDetached(Post $post, $relationName, $pivotIds)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['categories', 'tags'])) {
            $this->updated($post);

            self::$pivotEvent = true;
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
