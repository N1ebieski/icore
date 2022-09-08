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

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Category\Category;

class CategoryObserver
{
    /**
     * Handle the link "saving" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function saving(Category $category)
    {
        $category->real_depth = $category->getNextRealDepth();
    }

    /**
     * Handle the link "saved" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function saved(Category $category)
    {
        $category->reorderRealDepths();
    }

    /**
     * Handle the category "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        Cache::tags(['categories'])->flush();
    }

    /**
     * Handle the category "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        Cache::tags(['categories'])->flush();
    }

    /**
     * Handle the category "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $category->nextSiblings()->decrement($category->getPositionColumn());

        Cache::tags(['categories'])->flush();
    }

    /**
     * Handle the category "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the category "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }
}
