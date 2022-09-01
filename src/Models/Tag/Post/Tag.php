<?php

namespace N1ebieski\ICore\Models\Tag\Post;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Tag\Tag as BaseTag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Database\Factories\Tag\Post\TagFactory;

/**
 * @property string $name
 * @property string $normalized
 *
 */
class Tag extends BaseTag
{
    // Configuration

    /**
     * Create a new factory instance for the model.
     *
     * @return TagFactory
     */
    protected static function newFactory()
    {
        return TagFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        $table = Config::get('taggable.tables.taggable_taggables', 'taggable_taggables');

        return $this->morphedByMany(\N1ebieski\ICore\Models\Post::class, 'model', $table, 'tag_id');
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\Post::class;
    }

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
     * @return TagFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
