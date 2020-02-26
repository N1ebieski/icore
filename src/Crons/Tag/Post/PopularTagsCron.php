<?php

namespace N1ebieski\ICore\Crons\Tag\Post;

use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Jobs\Tag\Post\CachePopularTagsJob;
use N1ebieski\ICore\Crons\Tag\PopularTagsCron as BasePopularTagsCron;

/**
 * [PopularTagsCron description]
 */
class PopularTagsCron extends BasePopularTagsCron
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CachePopularTagJob $cachePopularTagJob
     */
    public function __construct(Category $category, CachePopularTagsJob $cachePopularTagsJob)
    {
        parent::__construct($category, $cachePopularTagsJob);
    }
}
