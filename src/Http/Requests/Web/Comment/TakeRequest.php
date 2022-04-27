<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Comment\Comment;

/**
 * @property Comment $comment
 */
class TakeRequest extends FormRequest
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
            'filter' => 'bail|array',
            'filter.orderby' => ['nullable', 'in:created_at|asc,created_at|desc,sum_rating|asc,sum_rating|desc'],
            'filter.except' => 'bail|filled|array',
            'filter.except.*' => 'bail|filled|integer'
        ];
    }
}
