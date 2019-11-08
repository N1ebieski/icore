<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\LogicCaptcha\Rules\LogicCaptcha;
use N1ebieski\ICore\Rules\Recaptcha_v2;

/**
 * [CaptchaComponent description]
 */
class CaptchaComponent implements Htmlable
{
    /**
     * [private description]
     * @var string
     */
    protected $driver;

    /**
     * [private description]
     * @var string
     */
    protected $site_key;

    /**
     * [private description]
     * @var int|null
     */
    protected $id;

    /**
     * [__construct description]
     * @param int|null $id [description]
     * @param Config $config [description]
     */
    public function __construct(Config $config, int $id = null)
    {
        $this->driver = $config->get('icore.captcha.driver');
        $this->site_key = $config->get('services.recaptcha_v2.site_key');
        $this->id = $id;
    }


    public function toHtml()
    {
        switch ($this->driver) {
            case 'recaptcha_v2':
                return view('icore::web.components.captcha.recaptcha_v2')->with(
                    'site_key',
                    $this->site_key
                );

            case 'logic_captcha':
                return view('icore::web.components.captcha.logic_captcha', ['id' => $this->id]);
        }
    }

    public function toRules()
    {
        switch ($this->driver) {
            case 'recaptcha_v2':
                return [
                    'g-recaptcha-response' => ['required', app()->make(Recaptcha_v2::class), 'no_js_validation']
                ];

            case 'logic_captcha':
                return [
                    'captcha' => ['required', 'string', app()->make(LogicCaptcha::class), 'no_js_validation']
                ];
        }
        return [];
    }

    public function toAttributes()
    {
        if ($this->driver === 'recaptcha_v2') {
            return [
                'g-recaptcha-response' => 'captcha'
            ];
        }
        return [];
    }
}
