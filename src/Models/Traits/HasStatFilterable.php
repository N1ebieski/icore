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

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use N1ebieski\ICore\ValueObjects\Stat\Slug;

trait HasStatFilterable
{
    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @param string $stat
     * @return Builder
     */
    public function scopeWithCountStats(Builder $query, string $stat): Builder
    {
        return $query->selectRaw("`stats_values`.`value` as `{$stat}`")
            ->leftJoin('stats_values', function (JoinClause $query) use ($stat) {
                return $query->on("{$this->getTable()}.id", '=', 'stats_values.model_id')
                    ->where('stats_values.model_type', $this->getMorphClass())
                    ->join('stats', function (JoinClause $query) use ($stat) {
                        return $query->on('stats_values.stat_id', '=', 'stats.id')
                            ->where('stats.slug', $stat);
                    });
            });
    }

    /**
     * [scopeFilterOrderBy description]
     * @param  Builder $query   [description]
     * @param  string|null  $orderby [description]
     * @return Builder           [description]
     */
    public function scopeFilterOrderBy(Builder $query, string $orderby = null): Builder
    {
        return $query->when(!is_null($orderby), function (Builder $query) use ($orderby) {
            // @phpstan-ignore-next-line
            $order = explode('|', $orderby);

            if (count($order) === 2) {
                return $query->when(
                    in_array($order[0], Slug::getAvailable()),
                    function ($query) use ($order) {
                        // @phpstan-ignore-next-line
                        return $query->withCountStats($order[0]);
                    }
                )
                ->orderBy($order[0], $order[1]);
            }

            return $query->latest();
        }, function (Builder $query) {
            return $query->latest();
        });
    }
}
