<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !in_array($this->mailing->status, [Mailing::ACTIVE, Mailing::INPROGRESS]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
