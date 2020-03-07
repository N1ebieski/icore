<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Models\Page\Page;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->page->isCommentable() === Page::WITHOUT_COMMENT) {
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
            'content' => 'required|min:3|max:10000',
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
