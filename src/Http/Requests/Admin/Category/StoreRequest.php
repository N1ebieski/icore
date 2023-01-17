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

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Contracts\Database\Query\Builder;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $category = new Category();
        $categoryLang = new CategoryLang();

        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'between:3,255',
                    function ($attribute, $value, $fail) use ($category, $categoryLang) {
                        $count = DB::table($categoryLang->getTable())
                            ->join($category->getTable(), "{$category->getTable()}.id", '=', "{$categoryLang->getTable()}.category_id")
                            ->when(is_null($this->input('parent_id')), function (Builder $query) use ($category) {
                                return $query->whereNull("{$category->getTable()}.parent_id");
                            }, function (Builder $query) use ($category) {
                                return $query->where("{$category->getTable()}.parent_id", $this->input('parent_id'));
                            })
                            ->where("{$categoryLang->getTable()}.{$attribute}", $value)
                            ->where("{$categoryLang->getTable()}.lang", Config::get('app.locale'))
                            ->count();

                        if ($count > 0) {
                            $fail(Lang::get('validation.unique', [$attribute]));
                        }
                    }
                ],
                'icon' => 'nullable|string|max:255',
                'parent_id' => 'nullable|integer|exists:categories,id'
            ],
            count(Config::get('icore.multi_langs')) > 1 ? [
                'auto_translate' => 'boolean',
                'progress' => 'integer|between:0,100'
            ] : []
        );
    }
}
