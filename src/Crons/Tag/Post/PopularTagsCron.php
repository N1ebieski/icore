<?php

namespace N1ebieski\ICore\Crons\Tag\Post;

use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Jobs\Tag\Post\CachePopularTags;
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
     * @param CachePopularTag $cachePopularTag
     */
    public function __construct(Category $category, CachePopularTags $cachePopularTags)
    {
        parent::__construct($category, $cachePopularTags);
    }
}
