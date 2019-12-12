<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment\Post;

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
        if ((bool)$this->post->comment === false) {
            abort(403, 'Adding comments has been disabled for this post.');
        }

        return $this->post->status === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|min:3|max:10000',
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
