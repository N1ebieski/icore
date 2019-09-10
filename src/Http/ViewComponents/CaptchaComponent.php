<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [CaptchaComponent description]
 */
class CaptchaComponent implements Htmlable
{
    /**
     * [private description]
     * @var string
     */
    private $driver;

    /**
     * [private description]
     * @var string
     */
    private $site_key;

    /**
     * [private description]
     * @var int|null
     */
    private $id;

    /**
     * [__construct description]
     * @param int|null $id [description]
     * @param Config $config [description]
     */
    public function __construct(Config $config, int $id = null)
    {
        $this->driver = $config->get('icore.captcha.driver');
        $this->site_key = $config->get('icore.captcha.configs.recaptcha_v2.site_key');
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
                    'g-recaptcha-response' => 'required|recaptcha_v2|no_js_validation'
                ];

            case 'logic_captcha':
                return [
                    'captcha' => 'required|string|captcha|no_js_validation'
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
