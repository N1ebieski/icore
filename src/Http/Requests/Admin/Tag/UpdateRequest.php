<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Tag;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Cviebrock\EloquentTaggable\Services\TagService;

class UpdateRequest extends FormRequest
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
                    ->ignore($this->tag->getKey(), $this->tag->getKeyName())
            ],
            'slug' => [
                'bail',
                'required',
                'string',
                'between:3,255',
                Rule::unique('tags', 'normalized')
                    ->ignore($this->tag->getKey(), $this->tag->getKeyName())
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
