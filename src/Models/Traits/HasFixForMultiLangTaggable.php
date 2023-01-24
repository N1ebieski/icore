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

namespace N1ebieski\ICore\Models\Traits;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Facades\Config;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFixForMultiLangTaggable
{
    use Taggable;

    // Relations

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        /** @var Tag */
        $model = Config::get('taggable.model');

        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Overrides

    /**
     * Remove all tags with specific lang from the model.
     *
     * @return self
     */
    public function detag(): self
    {
        $this->tags()->lang()->detach();

        return $this->load('tags');
    }
}
