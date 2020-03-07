<?php

namespace N1ebieski\ICore\Http\Requests\Web\Archive;

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
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'page' => 'integer'
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
        $data['month'] = $this->route('month');
        $data['year'] = $this->route('year');

        return $data;
    }
}
