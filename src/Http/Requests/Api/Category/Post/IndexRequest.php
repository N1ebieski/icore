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

namespace N1ebieski\ICore\Http\Requests\Api\Category\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\ValueObjects\Category\Status;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    /**
     *
     * @param Category $category
     * @return void
     */
    public function __construct(protected Category $category)
    {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'filter.parent' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists($this->category->getTable(), $this->category->getKeyName())
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type)
                            ->when(
                                !optional($this->user())->can('admin.categories.view'),
                                function ($query) {
                                    $query->where('status', Status::ACTIVE);
                                }
                            );
                    })
            ]
        ]);
    }
}
