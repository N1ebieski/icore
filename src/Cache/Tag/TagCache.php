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

namespace N1ebieski\ICore\Cache\Tag;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;

class TagCache
{
    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param Cache $cache
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(
        protected Tag $tag,
        protected Cache $cache,
        protected Config $config,
        protected Carbon $carbon
    ) {
        //
    }

    /**
     * [rememberBySlug description]
     * @param  string $slug [description]
     * @return Tag|null       [description]
     */
    public function rememberBySlug(string $slug): ?Tag
    {
        return $this->cache->remember(
            "tag.firstBySlug.{$slug}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($slug) {
                return $this->tag->makeRepo()->firstBySlug($slug);
            }
        );
    }

    /**
     * [rememberPopularByComponent description]
     * @param  array $component [description]
     * @return Collection [description]
     */
    public function rememberPopularByComponent(array $component): Collection
    {
        $json = json_encode($component);

        return $this->cache->remember(
            "tag.{$this->tag->poli}.getPopularByComponent.{$json}",
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes')),
            function () use ($component) {
                return $this->tag->makeRepo()->getPopularByComponent($component);
            }
        );
    }

    /**
     * Undocumented function
     *
     * @param Collection $tags
     * @param array $component
     * @return bool
     */
    public function putPopularByComponent(Collection $tags, array $component): bool
    {
        $json = json_encode($component);

        return $this->cache->put(
            "tag.{$this->tag->poli}.getPopularByComponent.{$json}",
            $tags,
            $this->carbon->now()->addMinutes($this->config->get('cache.minutes'))
        );
    }
}
