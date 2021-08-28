<?php

namespace N1ebieski\ICore\Http\Resources\Category;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        parent::__construct($category);
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
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'status' => [
                'value' => $this->status,
                'label' => Lang::get("icore::filter.status.{$this->status}")
            ],
            'real_depth' => $this->real_depth,
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->updated_at,
            'updated_at_diff' => $this->updated_at_diff,
            'ancestors' => App::make(CategoryResource::class)->collection($this->whenLoaded('ancestors'))
        ];
    }
}