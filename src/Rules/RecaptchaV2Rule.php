<?php

namespace N1ebieski\ICore\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Http\Clients\Recaptcha\V2\RecaptchaClient;

class RecaptchaV2Rule implements Rule
{
    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var RecaptchaClient
     */
    protected $client;

    /**
     * Undocumented function
     *
     * @param Lang $lang
     * @param RecaptchaClient $client
     */
    public function __construct(Lang $lang, RecaptchaClient $client)
    {
        $this->lang = $lang;
        $this->client = $client;
    }

    /**
     * [validate description]
     * @param  [type] $attribute  [description]
     * @param  [type] $value      [description]
     * @param  [type] $parameters [description]
     * @param  [type] $validator  [description]
     * @return [type]             [description]
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $response = $this->client->verify(['response' => $value]);
        } catch (\Exception $e) {
            return false;
        }

        return $response->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('icore::validation.captcha');
    }
}
