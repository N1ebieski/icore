<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Link;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Category\Category;

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
    protected function prepareForValidation()
    {
        $this->prepareUrlAttribute();
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute() : void
    {
        if ($this->has('url') && $this->input('url') !== null) {
            $this->merge(['url' => preg_replace('/(\/)$/', null, $this->input('url'))]);
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
            'name' => 'bail|required|string|between:3,255',
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ],
            'img' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:max_width=720,max_height=480',
            'delete_img' => 'nullable',
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
                    $query->where('status', Category::ACTIVE);
                })
            ],
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed|null  $keys
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['type'] = $this->route('type');

        return $data;
    }
}
