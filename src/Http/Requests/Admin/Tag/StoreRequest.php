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

namespace N1ebieski\ICore\Http\Requests\Admin\Tag;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Cviebrock\EloquentTaggable\Services\TagService;

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
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => Config::get('icore.tag.normalizer') !== null ?
                    call_user_func(Config::get('icore.tag.normalizer'), $this->input('name'))
                    : $this->input('name')
            ]);

            $this->merge([
                'slug' => App::make(TagService::class)->normalize($this->input('name'))
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
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'between:3,255',
                Rule::unique('tags', 'name')
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'between:3,255',
                Rule::unique('tags', 'normalized')
            ]
        ];
    }

    /**
     * Undocumented function
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            if (!$validator->errors()->has('name') && $validator->errors()->has('slug')) {
                $validator->errors()->add('name', $validator->errors()->first('slug'));
            }
        }

        parent::failedValidation($validator);
    }
}
