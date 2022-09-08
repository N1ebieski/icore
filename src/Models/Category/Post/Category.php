<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Models\Category\Post;

use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;
use N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory;

class Category extends BaseCategory
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\ICore\Models\Post::class,
        'status' => Status::ACTIVE
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

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory::new();
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

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CategoryFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
