<?php

namespace N1ebieski\ICore\Rules;

use Illuminate\Contracts\Validation\Rule;
use N1ebieski\ICore\Http\Clients\RecaptchaV2\Client;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;

class RecaptchaInvisibleRule implements Rule
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
     * @var Client
     */
    protected $client;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $secretKey;

    /**
     * Undocumented function
     *
     * @param Lang $lang
     * @param Config $config
     * @param Client $client
     */
    public function __construct(Lang $lang, Config $config, Client $client)
    {
        $this->lang = $lang;
        $this->client = $client;

        $this->secretKey = $config->get('services.recaptcha_invisible.secret_key');
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
            $this->client->post('/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $value
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return $this->client->getContents()->success;
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
