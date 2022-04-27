<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Comment\Status;

class UpdateStatusRequest extends FormRequest
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
            'status' => [
                'bail',
                'required',
                'integer',
                Rule::in([Status::ACTIVE, Status::INACTIVE])
            ],
        ];
    }
}
