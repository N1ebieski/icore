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

namespace N1ebieski\ICore\Http\Resources\Category;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Category
 */
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
     * @responseField id int
     * @responseField name string
     * @responseField slug string
     * @responseField icon string Class of icon.
     * @responseField status object Contains int value and string label.
     * @responseField real_depth int Level of hierarchy.
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField ancestors object[] Contains relationship Category ancestors (parent and higher).
     * @responseField meta object Paging, filtering and sorting information.
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
                'value' => $this->status->getValue(),
                'label' => Lang::get("icore::filter.status.{$this->status}")
            ],
            'real_depth' => $this->real_depth,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ancestors' => App::make(CategoryResource::class)->collection($this->whenLoaded('ancestors'))
        ];
    }
}
