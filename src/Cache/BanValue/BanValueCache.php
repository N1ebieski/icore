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

namespace N1ebieski\ICore\Cache\BanValue;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class BanValueCache
{
    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     * @param Cache    $cache    [description]
     * @param Config   $config   [description]
     * @param Str      $str      [description]
     * @param Carbon   $carbon   [description]
     */
    public function __construct(
        protected BanValue $banValue,
        protected Cache $cache,
        protected Config $config,
        protected Str $str,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * [rememberAllIpsAsString description]
     * @return string [description]
     */
    public function rememberAllIpsAsString(): string
    {
        return $this->cache->tags('bans.ip')->remember(
            "banValue.getAllIpsAsString",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                $ips = $this->banValue->where('type', Type::IP)->get();

                return $this->str->escaped($ips->implode('value', '|'));
            }
        );
    }

    /**
     * [rememberAllWordsAsString description]
     * @return string [description]
     */
    public function rememberAllWordsAsString(): string
    {
        return $this->cache->tags('bans.word')->remember(
            "banValue.{$this->config->get('app.locale')}.getAllWordsAsString",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                $words = $this->banValue->where('type', Type::WORD)->lang()->get();

                return $this->str->escaped($words->implode('value', '|'));
            }
        );
    }
}
