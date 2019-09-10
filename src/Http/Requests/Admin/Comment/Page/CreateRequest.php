<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ((bool)$this->page->comment === false) {
            abort(403, 'Adding comments has been disabled for this page.');
        }

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
            'parent_id' => [
                'required',
                'integer',
                Rule::exists('comments', 'id')->where(function($query) {
                    $query->where('status', 1);
                }),
            ]
        ];
    }
}
