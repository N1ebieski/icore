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

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Category\Status;

class IndexRequest extends FormRequest
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
        if ($this->has('filter.parent')) {
            $this->merge([
                'filter' => [
                    'real_parent' => $this->input('filter.parent'),
                    'parent' => !empty($this->input('filter.parent')) ?
                        $this->input('filter.parent')
                        : null
                    ] + $this->input('filter')
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->has('filter.parent')) {
            $this->merge([
                'filter' => [
                    'parent' => $this->input('filter.real_parent')
                ] + $this->input('filter')
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
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.except' => 'bail|filled|array',
            'filter.except.*' => 'bail|integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => [
                'bail',
                'nullable',
                'integer',
                Rule::in([Status::ACTIVE, Status::INACTIVE]),
                'no_js_validation'
            ],
            'filter.parent' => 'bail|nullable|integer|exists:categories,id|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate * 2), ($paginate * 4)]) . '|integer|no_js_validation'
        ];
    }
}
