<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Comment\Post;

use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Comment\Status;

/**
 * @property Post $post
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
        if ($this->post->comment->isInactive()) {
            App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'Adding comments has been disabled for this post.'
            );
        }

        return $this->post->status->isActive();
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
                    $query->where('status', Status::ACTIVE);
                }),
            ]
        ];
    }
}
