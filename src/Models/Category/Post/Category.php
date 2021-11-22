<?php

namespace N1ebieski\ICore\Models\Category\Post;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;

class Category extends BaseCategory
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\ICore\Models\Post::class,
        'status' => self::ACTIVE,
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Category\Category::class;
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\ICore\Models\Post::class, 'model', 'categories_models', 'category_id');
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'post';
    }
}
