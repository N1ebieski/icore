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

namespace N1ebieski\ICore\Http\Requests\Web\Comment;

use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Validation\ValidationException;

/**
 * @property Comment $comment
 */
class UpdateRequest extends FormRequest
{
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
        return $this->comment->status->isActive();
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

    /**
     *
     * @param array|int|string|null $key
     * @param mixed $default
     * @return array
     * @throws ValidationException
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if ($this->missing('content_html')) {
            return array_merge($validated, [
                'content_html' => $this->input('content')
            ]);
        }

        return $validated;
    }
}
