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
