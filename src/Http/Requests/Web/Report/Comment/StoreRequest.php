<?php

namespace N1ebieski\ICore\Http\Requests\Web\Report\Comment;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * @property Comment $comment
 */
class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->comment->status->isActive();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|string|min:3|max:255'
        ];
    }
}
