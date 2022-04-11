<?php

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
        $parent = $category->find($category->parent_id);

        $category->real_depth = $parent !== null ? $parent->real_depth + 1 : 0;
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
