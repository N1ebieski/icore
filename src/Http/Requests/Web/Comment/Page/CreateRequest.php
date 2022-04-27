<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment\Page;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Comment\Status;

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

        return $this->page->status->isActive();
    }

    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }
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
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Status::ACTIVE);
                }),
            ]
        ];
    }
}
