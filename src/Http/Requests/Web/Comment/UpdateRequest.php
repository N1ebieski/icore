<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment;

use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Comment\Comment;

class UpdateRequest extends FormRequest
{
    /**
     * [private description]
     * @var string
     */
    protected $bans;

    public function __construct(BanValue $banValue)
    {
        $this->bans = $banValue->makeCache()->rememberAllWordsAsString();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->comment->status === Comment::ACTIVE;
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
                !empty($this->bans) ? 'not_regex:/(.*)(\s|^)(' . $this->bans . ')(\s|\.|,|\?|$)(.*)/i' : null
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
            'content.not_regex' => Lang::get('icore::validation.not_regex_contains', [
                'words' => str_replace('|', ', ', $this->bans)
            ])
        ];
    }
}
