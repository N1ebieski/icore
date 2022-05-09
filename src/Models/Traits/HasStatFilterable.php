<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
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
            return $query->when(
                in_array($order[0], Slug::getAvailable()),
                function ($query) use ($order) {
                    $query->withCountStats($order[0]);
                }
            )
            ->orderBy($order[0] ?: 'updated_at', $order[1] ?: 'desc');
        }

        return $query->latest();
    }
}
