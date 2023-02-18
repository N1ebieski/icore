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

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

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
     * @responseField langs object[] Contains relationship CategoryLangs (available languages).
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'status' => [
                'value' => $this->status->getValue(),
                'label' => Lang::get("icore::filter.status.{$this->status}")
            ],
            'real_depth' => $this->real_depth,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ancestors' => $this->makeResource()->collection($this->whenLoaded('ancestors')),
            $this->mergeWhen(
                count(Config::get('icore.multi_langs')) > 1 && $this->relationLoaded('langs'),
                function () {
                    /** @var CategoryLang */
                    $categoryLang = $this->langs()->make();

                    return [
                        'langs' => $categoryLang->makeResource()
                            ->collection($this->langs->map(function ($item) {
                                $item->setAttribute('depth', 1);

                                return $item;
                            }))
                    ];
                }
            ),
        ];
    }
}
