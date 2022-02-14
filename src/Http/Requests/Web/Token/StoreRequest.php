<?php

namespace N1ebieski\ICore\Http\Requests\Web\Token;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|between:3,255',
            'expiration' => 'bail|nullable|integer',
            'abilities' => 'bail|required|array',
            'abilities.*' => [
                'bail',
                'string',
                'distinct',
                Rule::in($this->token::ABILITIES)
            ]
        ];
    }
}
