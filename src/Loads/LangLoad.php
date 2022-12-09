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

namespace N1ebieski\ICore\Loads;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository as Config;

class LangLoad
{
    /**
     *
     * @var string
     */
    protected string $lang;

    /**
     *
     * @var null|string
     */
    protected ?string $pref_lang;

    /**
     *
     * @param Config $config
     * @param Request $request
     * @return void
     */
    public function __construct(
        protected Config $config,
        protected Request $request
    ) {
        $this->lang = $this->config->get('app.locale');

        if (count($this->config->get('icore.multi_langs')) > 1) {
            if (
                $this->request->route('lang')
                && in_array($this->request->route('lang'), $this->config->get('icore.multi_langs'))
            ) {
                $this->lang = $this->request->route('lang');
            }

            $this->pref_lang = $this->lang;

            if (
                $this->request->user()
                && in_array($this->request->user()->pref_lang->getValue(), $this->config->get('icore.multi_langs'))
            ) {
                $this->pref_lang = $this->request->user()->pref_lang->getValue();
            }

            if (
                $this->request->cookie('lang_toggle')
                && in_array($this->request->cookie('lang_toggle'), $this->config->get('icore.multi_langs'))
            ) {
                $this->pref_lang = $this->request->cookie('lang_toggle');
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     *
     * @return null|string
     */
    public function getPrefLang(): ?string
    {
        return $this->pref_lang;
    }
}
