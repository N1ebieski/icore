<?php

namespace N1ebieski\ICore\Cache;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;

/**
 * [BanValueCache description]
 */
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
     * @var int
     */
    protected $minutes;

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
     */
    public function __construct(BanValue $banValue, Cache $cache, Config $config, Str $str)
    {
        $this->banValue = $banValue;
        $this->cache = $cache;
        $this->str = $str;
        $this->minutes = $config->get('cache.minutes');
    }

    /**
     * [rememberAllIpsAsString description]
     * @return string|null [description]
     */
    public function rememberAllIpsAsString() : ?string
    {
        return $this->cache->tags('bans.ip')->remember(
            "banValue.getAllIpsAsString",
            now()->addMinutes($this->minutes),
            function() {
                $ips = $this->banValue->whereType('ip')->get();

                return $this->str->escaped($ips->implode('value', '|'));
            }
        );
    }

    /**
     * [rememberAllWordsAsString description]
     * @return string|null [description]
     */
    public function rememberAllWordsAsString() : ?string
    {
        return $this->cache->tags('bans.word')->remember(
            "banValue.getAllWordsAsString",
            now()->addMinutes($this->minutes),
            function() {
                $words = $this->banValue->whereType('word')->get();

                return $this->str->escaped($words->implode('value', '|'));
            }
        );
    }
}
