<?php

namespace N1ebieski\ICore\Http\Requests\Web\Comment\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\View\Components\CaptchaComponent as Captcha;
use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Models\Post;

class StoreRequest extends FormRequest
{
    /**
     * [protected description]
     * @var Captcha
     */
    protected $captcha;

    /**
     * [private description]
     * @var string
     */
    protected $bans;

    /**
     * [__construct description]
     * @param Captcha  $captcha  [description]
     * @param BanValue $banValue [description]
     */
    public function __construct(Captcha $captcha, BanValue $banValue)
    {
        $this->captcha = $captcha;

        $this->bans = $banValue->makeCache()->rememberAllWordsAsString();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->post->comment === Post::WITHOUT_COMMENT) {
            App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'Adding comments has been disabled for this post.'
            );
        }

        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }
    }

    public function attributes()
    {
        return array_merge([], $this->captcha->toAttributes());
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
                !empty($this->bans) ? 'not_regex:/(.*)(\s|^)('.$this->bans.')(\s|\.|,|\?|$)(.*)/i' : null
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Comment::ACTIVE);
                }),
            ]
        ], $this->captcha->toRules());
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
