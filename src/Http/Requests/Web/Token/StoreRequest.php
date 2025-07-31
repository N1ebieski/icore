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

namespace N1ebieski\ICore\Http\Requests\Web\Token;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class StoreRequest extends FormRequest
{
    /**
     * Undocumented function
     *
     * @param Token $token
     */
    public function __construct(protected Token $token)
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
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->input('expiration') !== null) {
            $this->merge([
                'expiration' => (int)$this->input('expiration') * 24 * 60
            ]);
        }
    }

    /**
     *
     * @param string|null $key
     * @param mixed $default
     * @return array
     * @throws ValidationException
     */
    public function validated($key = null, $default = null): array
    {
        if ($this->input('expiration') !== null) {
            return array_merge(parent::validated($key, $default), [
                'expiration' => $this->input('expiration')
            ]);
        }

        return parent::validated($key, $default);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|between:3,255',
            'expiration' => 'bail|nullable|integer|max:1000',
            'abilities' => 'bail|required|array',
            'abilities.*' => [
                'bail',
                'string',
                'distinct',
                Rule::in($this->token::$abilities) //@phpstan-ignore-line
            ]
        ];
    }
}
