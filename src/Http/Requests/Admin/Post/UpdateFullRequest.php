<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Requests\Admin\Post;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\ValueObjects\Post\Status as PostStatus;
use N1ebieski\ICore\ValueObjects\Category\Status as CategoryStatus;

class UpdateFullRequest extends FormRequest
{
    /**
     * Constructor.
     * @param Category $category
     */
    public function __construct(protected Category $category)
    {
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('tags')) {
            $this->merge([
                'tags' => explode(
                    ',',
                    Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')($this->input('tags'))
                        : $this->input('tags')
                )
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
            'content_html' => 'bail|nullable|string',
            'tags' => 'array|between:0,' . Config::get('icore.post.max_tags'),
            'tags.*' => [
                'bail',
                'min:3',
                'distinct',
                'max:' . Config::get('icore.tag.max_chars'),
                'alpha_num_spaces'
            ],
            'categories' => 'required|array|between:1,' . Config::get('icore.post.max_categories'),
            'categories.*' => [
                'integer',
                'distinct',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where([
                        ['status', CategoryStatus::ACTIVE],
                        ['model_type', $this->category->model_type]
                    ]);
                }),
                'no_js_validation'
            ],
            'user' => 'bail|required|integer|exists:users,id',
            'seo_title' => 'max:255',
            'seo_desc' => 'max:255',
            'seo_noindex' => 'boolean',
            'seo_nofollow' => 'boolean',
            'comment' => 'boolean',
            'status' => [
                'bail',
                'required',
                'integer',
                Rule::in([PostStatus::ACTIVE, PostStatus::INACTIVE, PostStatus::SCHEDULED])
            ],
            'date_published_at' => 'required_unless:status,0|date|no_js_validation',
            'time_published_at' => 'required_unless:status,0|date_format:"H:i"|no_js_validation'
        ];
    }
}
