<?php

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
     * [private description]
     * @var BanValue
     */
    protected $banValue;

    /**
     * [protected description]
     * @var Cache
     */
    protected $cache;

    /**
     * [protected description]
     * @var Config
     */
    protected $config;

    /**
     * [protected description]
     * @var Carbon
     */
    protected $carbon;

    /**
     * [private description]
     * @var Str
     */
    protected $str;

    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     * @param Cache    $cache    [description]
     * @param Config   $config   [description]
     * @param Str      $str      [description]
     * @param Carbon   $carbon   [description]
     */
    public function __construct(
        BanValue $banValue,
        Cache $cache,
        Config $config,
        Str $str,
        Carbon $carbon
    ) {
        $this->banValue = $banValue;

        $this->cache = $cache;
        $this->config = $config;
        $this->str = $str;
        $this->carbon = $carbon;
    }

    /**
     * [rememberAllIpsAsString description]
     * @return string|null [description]
     */
    public function rememberAllIpsAsString(): ?string
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
     * @return string|null [description]
     */
    public function rememberAllWordsAsString(): ?string
    {
        return $this->cache->tags('bans.word')->remember(
            "banValue.getAllWordsAsString",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () {
                $words = $this->banValue->where('type', Type::WORD)->get();

                return $this->str->escaped($words->implode('value', '|'));
            }
        );
    }
}