<?php

namespace N1ebieski\ICore\Observers;

use N1ebieski\ICore\Models\Category\Category;

/**
 * [CategoryObserver description]
 */
class CategoryObserver
{
    /**
     * Handle the category "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        cache()->tags(['categories'])->flush();
    }

    /**
     * Handle the category "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        cache()->tags(['categories'])->flush();
    }

    /**
     * Handle the category "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Category\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        cache()->tags(['categories'])->flush();
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
