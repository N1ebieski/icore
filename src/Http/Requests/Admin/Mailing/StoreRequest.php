<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Mailing\Status;

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
            'title' => 'required|min:3|max:255',
            'status' => [
                'bail',
                'required',
                'integer',
                Rule::in([Status::ACTIVE, Status::INACTIVE, Status::SCHEDULED]),
                'no_js_validation'
            ],
            'users' => 'in:true,false|no_js_validation',
            'newsletter' => 'in:true,false|no_js_validation',
            'emails' => 'in:true,false|no_js_validation',
            'emails_json' => 'nullable|required_if:emails,true|json',
            'date_activation_at' => [
                'required_if:status,' . Status::SCHEDULED,
                'date',
                'no_js_validation'
            ],
            'time_activation_at' => [
                'required_if:status,' . Status::SCHEDULED,
                'date_format:"H:i"',
                'no_js_validation'
            ]
        ];
    }
}
