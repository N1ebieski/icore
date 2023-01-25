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

namespace N1ebieski\ICore\Http\Requests\Admin\Page;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Page\Status;

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

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }

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
        return array_merge(
            [
                'title' => 'required|min:3|max:255',
                'content_html' => 'bail|nullable|string',
                'tags' => 'array|between:0,' . Config::get('icore.page.max_tags'),
                'tags.*' => [
                    'bail',
                    'min:3',
                    'distinct',
                    'max:' . Config::get('icore.tag.max_chars'),
                    'alpha_num_spaces'
                ],
                'seo_title' => 'max:255',
                'seo_desc' => 'max:255',
                'icon' => 'nullable|string|max:255',
                'seo_noindex' => 'boolean',
                'seo_nofollow' => 'boolean',
                'comment' => 'boolean',
                'status' => [
                    'bail',
                    'required',
                    'integer',
                    Rule::in([Status::ACTIVE, Status::INACTIVE])
                ],
                'parent_id' => 'nullable|integer|exists:pages,id|no_js_validation'
            ],
            count(Config::get('icore.multi_langs')) > 1 ? [
                'auto_translate' => 'boolean'
            ] : []
        );
    }
}
