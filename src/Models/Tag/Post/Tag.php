<?php

namespace N1ebieski\ICore\Models\Tag\Post;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Tag\Tag as BaseTagModel;

class Tag extends BaseTagModel
{
    // Relations

    /**
     * [morphs description]
     * @return [type] [description]
     */
    public function morphs()
    {
        $table = Config::get('taggable.tables.taggable_taggables', 'taggable_taggables');

        return $this->morphedByMany(\N1ebieski\ICore\Models\Post::class, 'taggable', $table, 'tag_id');
    }

    // Accessors

    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\ICore\\Models\\Post';
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'post';
    }
}
