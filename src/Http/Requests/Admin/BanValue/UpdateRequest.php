<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanValue;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property BanValue $banValue
 */
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
            'value' => [
                'required',
                'string',
                Rule::unique('bans_values', 'value')->where(function ($query) {
                    $query->where('type', $this->banValue->type->getValue());
                })
            ],
        ];
    }
}
