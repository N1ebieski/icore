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

namespace N1ebieski\ICore\Rules;

use Stringable;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Http\Clients\Google\Recaptcha\V2\RecaptchaClient;

class RecaptchaV2Rule implements Rule, Stringable
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
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator): bool
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
    public function passes($attribute, $value): bool
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

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'recaptcha_v2';
    }
}
