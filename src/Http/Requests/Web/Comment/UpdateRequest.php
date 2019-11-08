<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\BanValue;

class UpdateRequest extends FormRequest
{
    /**
     * [private description]
     * @var string
     */
    protected $bans;

    public function __construct(BanValue $banValue)
    {;
        $this->bans = $banValue->getCache()->rememberAllWordsAsString();
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => [
                'required',
                'min:3',
                'max:10000',
                'not_regex:/(.*)(\s|^)('.$this->bans.')(\s|$)(.*)/i'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'content.not_regex' => trans('icore::validation.not_regex_contains', ['words' => str_replace('|', ', ', $this->bans)])
        ];
    }
}
