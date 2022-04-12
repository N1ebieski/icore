<?php

namespace N1ebieski\ICore\Database\Factories\Category\Post;

use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Database\Factories\Category\CategoryFactory as BaseCategoryFactory;

class CategoryFactory extends BaseCategoryFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;
}
