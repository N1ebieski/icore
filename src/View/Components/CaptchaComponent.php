<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CaptchaComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * [private description]
     * @var Config
     */
    protected $config;

    /**
     * [private description]
     * @var int|null
     */
    protected $id;

    /**
     * Undocumented function
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param integer $id
     */
    public function __construct(ViewFactory $view, Config $config, int $id = null)
    {
        $this->view = $view;
        $this->config = $config;

        $this->id = $id;
    }

    /**
     * Undocumented function
     *
     * @return View|void
     */
    public function toHtml()
    {
        switch ($this->config->get('icore.captcha.driver')) {
            case 'recaptcha_invisible':
                return $this->view->make('icore::web.components.captcha.recaptcha_invisible', [
                    'site_key' => $this->config->get('services.recaptcha_invisible.site_key')
                ]);

            case 'recaptcha_v2':
                return $this->view->make('icore::web.components.captcha.recaptcha_v2', [
                    'site_key' => $this->config->get('services.recaptcha_v2.site_key')
                ]);

            case 'logic_captcha':
                return $this->view->make('icore::web.components.captcha.logic_captcha', [
                    'id' => $this->id
                ]);
        }
    }
}
