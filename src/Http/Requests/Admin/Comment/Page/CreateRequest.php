<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment\Page;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Page\Comment;

/**
 * @property Page $page
 */
class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->page->comment->isInactive()) {
            App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'Adding comments has been disabled for this page.'
            );
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
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Comment::ACTIVE);
                }),
            ]
        ];
    }
}
