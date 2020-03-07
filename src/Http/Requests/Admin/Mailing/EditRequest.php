<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Mailing;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->mailing->status !== Mailing::ACTIVE;
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
