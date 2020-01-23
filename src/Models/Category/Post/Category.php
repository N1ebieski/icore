<?php

namespace N1ebieski\ICore\Models\Category\Post;

use N1ebieski\ICore\Models\Category\Category as CategoryBaseModel;

/**
 * [Category description]
 */
class Category extends CategoryBaseModel
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => 'N1ebieski\\ICore\\Models\\Post',
        'status' => 1,
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\ICore\\Models\\Category\\Category';
    }

    // Relations

    /**
     * [morphs description]
     * @return [type] [description]
     */
    public function morphs()
    {
        return $this->morphedByMany('N1ebieski\ICore\Models\Post', 'model', 'categories_models', 'category_id');
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'post';
    }
}
