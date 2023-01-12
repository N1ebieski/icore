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

use Exception;
use Illuminate\Http\Request;
use N1ebieski\ICore\Http\Clients\GeoIP\GeoIPClient;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;

class LangLoad
{
    /**
     *
     * @var string
     */
    protected string $lang;

    /**
     *
     * @param Config $config
     * @param Request $request
     * @param GeoIPClient $client
     * @return void
     * @throws ConflictingHeadersException
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function __construct(
        protected Config $config,
        protected Request $request,
        protected GeoIPClient $client
    ) {
        $this->lang = $this->config->get('app.locale');

        if (
            count($this->config->get('icore.multi_langs')) > 1
            && is_string($lang = $this->getLangFromRoute())
        ) {
            $this->lang = $lang;
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
     * @return string
     */
    public function getPrefLang(): string
    {
        if (is_string($lang = $this->getLangFromCookie())) {
            return $lang;
        }

        if (is_string($lang = $this->getLangFromUser())) {
            return $lang;
        }

        if (is_string($lang = $this->getLangFromGeolocation())) {
            return $lang;
        }

        return $this->getLang();
    }

    /**
     *
     * @return null|string
     */
    public function getLangFromRoute(): ?string
    {
        if (
            $this->request->route('lang')
            && in_array($this->request->route('lang'), $this->config->get('icore.multi_langs'))
        ) {
            return $this->request->route('lang');
        }

        return null;
    }

    /**
     *
     * @return null|string
     */
    public function getLangFromCookie(): ?string
    {
        if (
            $this->request->cookie('lang_toggle')
            && in_array($this->request->cookie('lang_toggle'), $this->config->get('icore.multi_langs'))
        ) {
            /** @var string */
            return $this->request->cookie('lang_toggle');
        }

        return null;
    }

    /**
     *
     * @return null|string
     */
    public function getLangFromUser(): ?string
    {
        if (
            $this->request->user()
            && in_array($this->request->user()->pref_lang->getValue(), $this->config->get('icore.multi_langs'))
        ) {
            return $this->request->user()->pref_lang->getValue();
        }

        return null;
    }

    /**
     *
     * @return null|string
     * @throws ConflictingHeadersException
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function getLangFromGeolocation(): ?string
    {
        $client = $this->client->location(['ip' => $this->request->ip()]);

        if (
            $client->get('default') === false
            && is_string($client->get('language'))
            && in_array($client->get('language'), $this->config->get('icore.multi_langs'))
        ) {
            return $client->get('language');
        }

        return null;
    }
}
