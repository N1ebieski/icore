<?php

namespace N1ebieski\ICore\Http\Requests\Web\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

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
                'unique:users,name, ' . Auth::user()->id
            ],
            'marketing_agreement' => 'bail|nullable|boolean'
        ];
    }
}
