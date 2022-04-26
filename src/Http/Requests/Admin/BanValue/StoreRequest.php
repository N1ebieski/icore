<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanValue;

use Illuminate\Validation\Rule;
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->route('type')) {
            $this->merge([
                'type' => $this->route('type')
            ]);
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
            'type' => 'required|string|in:ip,word',
            'value' => [
                'required',
                'string',
                Rule::unique('bans_values', 'value')->where(function ($query) {
                    $query->where('type', $this->input('type'));
                })
            ]
        ];
    }
}
