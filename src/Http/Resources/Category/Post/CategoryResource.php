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

namespace N1ebieski\ICore\Http\Resources\Category\Post;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource as BaseCategoryResource;

/**
 * @mixin Category
 */
class CategoryResource extends BaseCategoryResource
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
        return array_merge(parent::toArray($request), [
            'links' => [
                $this->mergeWhen(
                    Config::get('icore.routes.web.enabled') === true && $this->status->isActive(),
                    function () {
                        return [
                            'web' => URL::route('web.category.post.show', [$this->slug])
                        ];
                    }
                ),
                $this->mergeWhen(
                    Config::get('icore.routes.admin.enabled') === true && $request->user()?->can('admin.categories.view'),
                    function () {
                        return [
                            'admin' => URL::route('admin.category.post.index', ['filter[search]' => 'id:"' . $this->id . '"'])
                        ];
                    }
                )
            ]
        ]);
    }
}
