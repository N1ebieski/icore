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

namespace N1ebieski\ICore\Http\Requests\Admin\Link;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as BaseRequest;
use N1ebieski\ICore\ValueObjects\Link\Type;

class IndexRequest extends FormRequest
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
        if ($this->route('type')) {
            $filter = [
                'filter' => $this->input('filter', []) + [
                    'type' => $this->route('type')
                ]
            ];

            App::make(BaseRequest::class)->merge($filter);

            $this->merge($filter);
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
            'page' => 'integer',
            'filter' => 'bail|array',
            'filter.type' => [
                'bail',
                'required',
                'string',
                Rule::in([Type::LINK, Type::BACKLINK])
            ],
            'filter.except' => 'bail|filled|array',
            'filter.except.*' => 'bail|integer'
        ];
    }
}
