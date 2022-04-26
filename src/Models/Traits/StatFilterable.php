<?php

namespace N1ebieski\ICore\Models\Traits;

use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Stat\Slug;

trait StatFilterable
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
            ->leftJoin('stats_values', function ($query) use ($stat) {
                $query->on("{$this->getTable()}.id", '=', 'stats_values.model_id')
                    ->where('stats_values.model_type', $this->getMorphClass())
                    ->join('stats', function ($query) use ($stat) {
                        $query->on('stats_values.stat_id', '=', 'stats.id')
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
        $order = explode('|', $orderby);

        if (count($order) === 2) {
            try {
                Slug::fromString($order[0]);

                $available = true;
            } catch (\InvalidArgumentException $e) {
                $available = false;
            }

            return $query->when($available, function ($query) use ($order) {
                $query->withCountStats($order[0]);
            })
            ->orderBy($order[0], $order[1]);
        }

        return $query->latest();
    }
}
