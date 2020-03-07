<?php

namespace N1ebieski\ICore\Http\Requests\Web\Contact;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Http\ViewComponents\CaptchaComponent as Captcha;

class SendRequest extends FormRequest
{
    /**
     * Undocumented variable
     *
     * @var Captcha
     */
    protected $captcha;

    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
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
            'email' => 'required|email',
            'title' => 'required',
            'content' => 'required'
        ], $this->captcha->toRules());
    }
}
