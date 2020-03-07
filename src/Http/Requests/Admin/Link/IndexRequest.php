<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Link;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'bail|required|string|in:link,backlink',
            'page' => 'integer',
            'except' => 'filled|array',
            'except.*' => 'integer'
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
