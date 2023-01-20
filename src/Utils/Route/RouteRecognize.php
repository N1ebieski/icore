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

namespace N1ebieski\ICore\Utils\Route;

use LogicException;
use Illuminate\Routing\Route;
use InvalidArgumentException;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Routing\UrlGenerator as URL;

class RouteRecognize
{
    /**
     *
     * @param Route $route
     * @param URL $url
     * @return void
     */
    public function __construct(
        protected Route $route,
        protected URL $url,
        protected Collect $collect
    ) {
        //
    }

    /**
     *
     * @param string $lang
     * @return string|false
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public function getCurrentUrlWithLang(string $lang): string|false
    {
        $newParameters = $this->getNewParametersWithLang($lang);

        if ($newParameters === false) {
            return false;
        }

        return $this->url->route($this->route->getName(), $newParameters);
    }

    /**
     *
     * @param string $lang
     * @return array|false
     * @throws LogicException
     */
    protected function getNewParametersWithLang(string $lang): array|false
    {
        $originalParameters = $this->route->originalParameters();

        $break = false;

        $newParameters = $this->collect->make($this->route->parameters())
            ->map(function ($item, $key) use ($lang, $originalParameters, &$break) {
                if ($item instanceof \Illuminate\Database\Eloquent\Model) {
                    if (
                        preg_match('/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/', $originalParameters[$key])
                        && !is_null($item->uuid)
                    ) {
                        return $originalParameters[$key];
                    }

                    if (
                        filter_var($originalParameters[$key], FILTER_VALIDATE_INT)
                        && $this->route->wheres[$key] === '[0-9]+'
                        && !is_null($item->id)
                    ) {
                        return $originalParameters[$key];
                    }

                    if (
                        is_string($originalParameters[$key])
                        && $item->relationLoaded('langs')
                        && ($itemLang = $item->langs->firstWhere('lang', $lang))
                        && !is_null($itemLang->slug)
                    ) {
                        return $itemLang->slug;
                    }

                    $break = true;
                }

                return $originalParameters[$key];
            })
            ->merge(['lang' => $lang])
            ->toArray();

        return $break === false ? $newParameters : false;
    }
}
