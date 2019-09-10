<?php

namespace N1ebieski\ICore\Http\Requests\Web\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                Rule::unique('newsletters', 'email')->where(function($query) {
                    $query->whereStatus(1);
                })
            ]
        ];
    }
}
