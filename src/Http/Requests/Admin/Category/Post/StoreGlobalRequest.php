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

namespace N1ebieski\ICore\Http\Requests\Admin\Category\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest as BaseStoreGlobalRequest;

class StoreGlobalRequest extends BaseStoreGlobalRequest
{
    /**
     * @param Category $category
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
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type);
                    })
            ]
        ]);
    }
}
