<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\LogicCaptcha\Rules\LogicCaptchaRule;
use N1ebieski\ICore\Rules\RecaptchaV2Rule;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [CaptchaComponent description]
 */
class CaptchaComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

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
     * Undocumented function
     *
     * @param ViewFactory $view
     * @param App $app
     * @param Config $config
     * @param integer $id
     */
    public function __construct(ViewFactory $view, App $app, Config $config, int $id = null)
    {
        $this->view = $view;
        $this->app = $app;

        $this->driver = $config->get('icore.captcha.driver');
        $this->site_key = $config->get('services.recaptcha_v2.site_key');
        $this->id = $id;
    }

    /**
     * Undocumented function
     *
     * @return View|void
     */
    public function toHtml()
    {
        switch ($this->driver) {
            case 'recaptcha_v2':
                return $this->view->make('icore::web.components.captcha.recaptcha_v2')
                    ->with('site_key', $this->site_key);

            case 'logic_captcha':
                return $this->view->make('icore::web.components.captcha.logic_captcha', [
                    'id' => $this->id
                ]);
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toRules() : array
    {
        switch ($this->driver) {
            case 'recaptcha_v2':
                return [
                    'g-recaptcha-response' => [
                        'required',
                        $this->app->make(RecaptchaV2Rule::class),
                        'no_js_validation'
                    ]
                ];

            case 'logic_captcha':
                return [
                    'captcha' => [
                        'required',
                        'string',
                        $this->app->make(LogicCaptchaRule::class),
                        'no_js_validation'
                    ]
                ];

            default:
                return [];
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toAttributes() : array
    {
        if ($this->driver === 'recaptcha_v2') {
            return [
                'g-recaptcha-response' => 'captcha'
            ];
        }

        return [];
    }
}
