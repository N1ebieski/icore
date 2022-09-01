<?php

namespace N1ebieski\ICore\Http\Resources\Tag;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tag
 */
class TagResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->tag_id,
            'name' => $this->name,
            'slug' => $this->normalized,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
