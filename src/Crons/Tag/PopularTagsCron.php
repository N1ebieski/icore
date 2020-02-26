<?php

namespace N1ebieski\ICore\Crons\Tag;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Jobs\Tag\CachePopularTagsJob;

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
     * @var CachePopularTagsJob
     */
    protected $cachePopularTagsJob;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CachePopularTagJob $cachePopularTagJob
     */
    public function __construct(Category $category, CachePopularTagsJob $cachePopularTagsJob)
    {
        $this->category = $category;
        $this->cachePopularTagsJob = $cachePopularTagsJob;
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
        $this->cachePopularTagsJob->dispatch();

        $categories = $this->category->all();

        foreach ($categories as $category) {
            $this->cachePopularTagsJob->dispatch([$category->id]);
        }
    }
}
