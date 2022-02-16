<?php

namespace N1ebieski\ICore\Http\Requests\Api\Token;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class StoreRequest extends FormRequest
{
    /**
     * Undocumented variable
     *
     * @var Token
     */
    protected $token;

    /**
     * Undocumented function
     *
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        parent::__construct();

        $this->token = $token;
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
     * Undocumented function
     *
     * @return array
     */
    public function validated(): array
    {
        if ($this->input('expiration') !== null) {
            return array_merge(parent::validated(), [
                'expiration' => $this->input('expiration')
            ]);
        }

        return parent::validated();
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
                'required',
                'string',
                'distinct',
                Rule::in($this->token::ABILITIES)
            ]
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'example' => 'Access'
            ],
            'expiration' => [
                'description' => 'Token expiration time on days. If null is unlimited.',
                'example' => 7
            ],
            'abilities.*' => [
                'description' => 'Array containing scopes. Wildcard supported.',
                'example' => ['api.*']
            ]
        ];
    }
}
