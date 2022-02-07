<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment\Page;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Traits\CaptchaExtended;

class StoreRequest extends FormRequest
{
    use CaptchaExtended;

    /**
     * [private description]
     * @var string
     */
    protected $bans;

    /**
     * Undocumented function
     *
     * @param BanValue $banValue
     */
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
        if ($this->page->comment === Page::WITHOUT_COMMENT) {
            App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'Adding comments has been disabled for this page.'
            );
        }

        return $this->page->status === Page::ACTIVE;
    }

    /**
     * Undocumented function
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
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function attributes()
    {
        return array_merge([], $this->prepareCaptchaAttributes());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'content' => [
                'required',
                'min:3',
                'max:10000',
                !empty($this->bans) ? 'not_regex:/(.*)(\s|^)(' . $this->bans . ')(\s|\.|,|\?|$)(.*)/i' : null
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Comment::ACTIVE);
                }),
            ]
        ], $this->prepareCaptchaRules());
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
