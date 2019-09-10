<?php

namespace N1ebieski\ICore\Http\Requests\Web\Page;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
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
            'orderby' => ['in:created_at|asc,created_at|desc,sum_rating|asc,sum_rating|desc']
        ];
    }
}
