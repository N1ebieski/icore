<?php

namespace N1ebieski\ICore\Http\Requests\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => [
                'required',
                'alpha_dash',
                'max:255',
                'unique:users,name'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'privacy_agreement' => 'bail|accepted',
            'contact_agreement' => 'bail|accepted',
            'marketing_agreement' => 'bail|nullable|boolean'
        ];
    }
}
