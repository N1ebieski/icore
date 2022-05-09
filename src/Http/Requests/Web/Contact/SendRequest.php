<?php

namespace N1ebieski\ICore\Http\Requests\Web\Contact;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Http\Requests\Traits\HasCaptcha;

class SendRequest extends FormRequest
{
    use HasCaptcha;

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
        return array_merge([
            'email' => 'required|email',
            'title' => 'required',
            'content' => 'required',
            'contact_agreement' => 'bail|accepted'
        ], $this->prepareCaptchaRules());
    }
}
