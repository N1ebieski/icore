<?php

namespace N1ebieski\ICore\Database\Factories\Tag\Post;

use N1ebieski\ICore\Models\Tag\Post\Tag;
use N1ebieski\ICore\Database\Factories\Tag\TagFactory as BaseTagFactory;

class TagFactory extends BaseTagFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;
}
