<?php

namespace N1ebieski\ICore\Http\Requests\Admin\BanValue;

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
        $type = $this->type;

        return [
            'type' => 'required|string|in:ip,word',
            'value' => [
                'required',
                'string',
                Rule::unique('bans_values', 'value')->where(function($query) use ($type) {
                    $query->where('type', $type);
                })
            ]
        ];
    }

    public function all($keys = null)
    {
       $data = parent::all($keys);
       $data['type'] = $this->route('type');

       return $data;
    }
}
