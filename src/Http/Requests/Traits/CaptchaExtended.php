<?php

namespace N1ebieski\ICore\Http\Requests\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Rules\RecaptchaV2Rule;
use N1ebieski\ICore\Rules\RecaptchaInvisibleRule;
use N1ebieski\LogicCaptcha\Rules\LogicCaptchaRule;
use N1ebieski\LogicCaptcha\Rules\LogicCaptchaApiRule;

trait CaptchaExtended
{
    /**
     * [prepareFieldsRules description]
     * @return array [description]
     */
    protected function prepareCaptchaRules(): array
    {
        switch (Config::get('icore.captcha.driver')) {
            case 'recaptcha_invisible':
                return [
                    'g-recaptcha-response' => [
                        'required',
                        App::make(RecaptchaInvisibleRule::class),
                        'no_js_validation'
                    ]
                ];

            case 'recaptcha_v2':
                return [
                    'g-recaptcha-response' => [
                        'required',
                        App::make(RecaptchaV2Rule::class),
                        'no_js_validation'
                    ]
                ];

            case 'logic_captcha':
                if ($this->has('key')) {
                    return [
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
                    ];
                } else {
                    return [
                        'captcha' => [
                            'required',
                            'string',
                            App::make(LogicCaptchaRule::class),
                            'no_js_validation'
                        ]
                    ];
                }

            default:
                return [];
        }
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

    /**
     * Undocumented function
     *
     * @return array
     */
    protected function prepareCaptchaAttributes(): array
    {
        // if (Config::get('icore.captcha.driver') === 'recaptcha_v2') {
        //     return [
        //         'g-recaptcha-response' => 'captcha'
        //     ];
        // }

        return [];
    }
}
