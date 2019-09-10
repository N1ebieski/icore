<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

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
            'title' => 'required|min:3|max:255',
            'status' => 'required|in:0,1,2|no_js_validation',
            'users' => 'in:true,false|no_js_validation',
            'newsletter' => 'in:true,false|no_js_validation',
            'emails' => 'in:true,false|no_js_validation',
            'emails_json' => 'required_if:emails,true|json',
            'date_activation_at' => 'required_if:status,2|date|no_js_validation',
            'time_activation_at' => 'required_if:status,2|date_format:"H:i"|no_js_validation'
        ];
    }
}
