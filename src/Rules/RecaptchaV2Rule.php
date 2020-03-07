<?php

namespace N1ebieski\ICore\Rules;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;

/**
 * [Recaptcha_v2 description]
 */
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
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var object|GuzzleResponse
     */
    protected $response;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected string $secretKey;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Undocumented function
     *
     * @param Lang $lang
     * @param Config $config
     * @param GuzzleClient $guzzle
     */
    public function __construct(Lang $lang, Config $config, GuzzleClient $guzzle)
    {
        $this->lang = $lang;
        $this->guzzle = $guzzle;

        $this->secretKey = $config->get('services.recaptcha_v2.secret_key');
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
     * Undocumented function
     *
     * @param string $value
     * @return GuzzleResponse
     */
    protected function makeResponse(string $value) : GuzzleResponse
    {
        return $this->response = $this->guzzle->request('POST', $this->verifyUrl, [
            'form_params' => [
                'secret' => $this->secretKey,
                'response' => $value
            ]
        ]);
    }

    /**
     * Undocumented function
     *
     * @return object
     */
    protected function prepareResponse() : object
    {
        return json_decode($this->response->getBody());
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
            $this->makeResponse($value);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return false;
        }

        return $this->prepareResponse()->success;
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
