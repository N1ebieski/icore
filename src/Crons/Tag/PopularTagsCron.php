<?php

namespace N1ebieski\ICore\Crons\Tag;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Jobs\Tag\CachePopularTags;

/**
 * [PopularTagsCron description]
 */
class PopularTagsCron
{
    /**
     * [private description]
     * @var Category
     */
    protected $category;   

    /**
     * [private description]
     * @var CachePopularTags
     */
    protected $cachePopularTags;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CachePopularTag $cachePopularTag
     */
    public function __construct(Category $category, CachePopularTags $cachePopularTags)
    {
        $this->category = $category;
        $this->cachePopularTags = $cachePopularTags;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->addToQueue();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function addToQueue() : void
    {
        $this->cachePopularTags->dispatch();

        $categories = $this->category->all();

        foreach ($categories as $category) {
            $this->cachePopularTags->dispatch([$category->id]);
        }
    }
}
