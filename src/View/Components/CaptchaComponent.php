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

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CaptchaComponent extends Component
{
    /**
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param null|int $id
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected Config $config,
        protected ?int $id = null
    ) {
        //
    }

    /**
     *
     * @return View
     * @throws CustomException
     */
    public function render(): View
    {
        return match ($this->config->get('icore.captcha.driver')) {
            'recaptcha_invisible' => $this->view->make('icore::web.components.captcha.recaptcha_invisible', [
                'site_key' => $this->config->get('services.recaptcha_invisible.site_key')
            ]),

            'recaptcha_v2' => $this->view->make('icore::web.components.captcha.recaptcha_v2', [
                'site_key' => $this->config->get('services.recaptcha_v2.site_key')
            ]),

            'logic_captcha' => $this->view->make('icore::web.components.captcha.logic_captcha', [
                'id' => $this->id
            ]),

            default => throw new CustomException(
                "Captcha driver \"{$this->config->get('icore.captcha.driver')}\" was not found",
                HttpResponse::HTTP_FORBIDDEN
            )
        };
    }
}
