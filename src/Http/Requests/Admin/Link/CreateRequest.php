<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Link;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['type'] = $this->route('type');

        return $data;
    }
}
