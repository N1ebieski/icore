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

namespace N1ebieski\ICore\Http\Requests\Admin\Link;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Link\Type;
use N1ebieski\ICore\ValueObjects\Category\Status;

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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->prepareTypeAttribute();

        $this->prepareUrlAttribute();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareTypeAttribute()
    {
        if ($this->route('type')) {
            $this->merge([
                'type' => $this->route('type')
            ]);
        }
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute(): void
    {
        if ($this->has('url') && $this->input('url') !== null) {
            $this->merge(['url' => preg_replace('/(\/)$/', '', $this->input('url'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                'bail',
                'required',
                'string',
                Rule::in([Type::LINK, Type::BACKLINK])
            ],
            'name' => 'bail|required|string|between:3,255',
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ],
            'img' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:max_width=720,max_height=480',
            'home' => 'bail|nullable|boolean',
            'categories' => [
                'bail',
                'nullable',
                'array'
            ],
            'categories.*' => [
                'bail',
                'integer',
                'distinct',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('status', Status::ACTIVE);
                })
            ],
        ];
    }
}
