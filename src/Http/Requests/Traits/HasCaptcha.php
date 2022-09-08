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

namespace N1ebieski\ICore\Http\Requests\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Rules\RecaptchaV2Rule;
use N1ebieski\ICore\Rules\RecaptchaInvisibleRule;
use N1ebieski\LogicCaptcha\Rules\LogicCaptchaRule;
use N1ebieski\LogicCaptcha\Rules\LogicCaptchaApiRule;

trait HasCaptcha
{
    /**
     * [prepareFieldsRules description]
     * @return array [description]
     */
    protected function prepareCaptchaRules(): array
    {
        return match (Config::get('icore.captcha.driver')) {
            'recaptcha_invisible' => [
                'g-recaptcha-response' => [
                    'required',
                    App::make(RecaptchaInvisibleRule::class),
                    'no_js_validation'
                ]
            ],

            'recaptcha_v2' => [
                'g-recaptcha-response' => [
                    'required',
                    App::make(RecaptchaV2Rule::class),
                    'no_js_validation'
                ]
            ],

            'logic_captcha' => $this->has('key') ? [
                'key' => [
                    'bail',
                    'required',
                    'array',
                ],
                'captcha' => [
                    'bail',
                    'required',
                    'string',
                    App::make(LogicCaptchaApiRule::class)
                ]
            ] : [
                'captcha' => [
                    'required',
                    'string',
                    App::make(LogicCaptchaRule::class),
                    'no_js_validation'
                ]
            ],

            default => []
        };
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function prepareCaptchaBodyParameters(): array
    {
        return [
            'g-recaptcha-response' => [
                'description' => 'Recaptcha response.'
            ],
            'key' => [
                'description' => 'Array contains encode logic captcha keys.'
            ],
            'captcha' => [
                'description' => 'Logic captcha response.'
            ]
        ];
    }
}
