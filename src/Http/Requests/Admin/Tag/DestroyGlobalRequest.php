<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Tag;

use Illuminate\Foundation\Http\FormRequest;

class DestroyGlobalRequest extends FormRequest
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
            'select' => 'required|array|min:1',
            'select.*' => 'integer'
        ];
    }
}
