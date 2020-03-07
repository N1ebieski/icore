<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Category\Post\Category;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('tags')) {
            $this->merge([
                'tags' => explode(',', $this->get('tags'))
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
            'title' => 'required|min:3|max:255',
            'tags' => 'array|between:0,' . Config::get('icore.post.max_tags'),
            'tags.*' => 'min:3|max:30|alpha_num_spaces',
            'categories' => 'required|array|between:1,' . Config::get('icore.post.max_categories'),
            'categories.*' => [
                'integer',
                'distinct',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where([
                        ['status', Category::ACTIVE],
                        ['model_type', 'N1ebieski\ICore\\Models\\Post']
                    ]);
                }),
            ],
            'seo_title' => 'max:255',
            'seo_desc' => 'max:255',
            'seo_noindex' => 'boolean',
            'seo_nofollow' => 'boolean',
            'comment' => 'boolean',
            'status' => 'required|in:0,1,2',
            'date_published_at' => 'required_unless:status,0|date|no_js_validation',
            'time_published_at' => 'required_unless:status,0|date_format:"H:i"|no_js_validation'
        ];
    }
}
