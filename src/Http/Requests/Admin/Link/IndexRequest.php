<?php

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
