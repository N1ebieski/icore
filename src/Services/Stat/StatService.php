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

namespace N1ebieski\ICore\Services\Stat;

use Exception;
use Throwable;
use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class StatService
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param DB $db
     */
    public function __construct(
        protected Stat $stat,
        protected DB $db
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function increment(): bool
    {
        if (!$this->stat->relationLoaded('morph')) {
            throw new \Exception('Stat model must have morph model.');
        }

        return $this->db->transaction(function () {
            // @phpstan-ignore-next-line
            $this->stat->morph
                ->stats()
                ->syncWithoutDetaching([
                    $this->stat->id => ['value' => $this->db->raw("`value` + 1")]
                ]);

            return true;
        });
    }

    /**
     *
     * @return int
     * @throws Exception
     * @throws Throwable
     */
    public function incrementGlobal(): int
    {
        if (!$this->stat->relationLoaded('morphs')) {
            throw new \Exception('Stat model must have morphs collection.');
        }

        return $this->db->transaction(function () {
            /** @var Collection */
            $morphs = $this->stat->morphs;

            $morphs->filter(function (mixed $morph) {
                return $morph->stats->doesntContain('slug', $this->stat->slug->getValue());
            })
            ->each(function (mixed $morph) {
                $this->stat->setRelations(['morph' => $morph])
                    ->makeService()
                    ->increment();
            });

            /** @var MorphToMany */
            $morphsRelation = $this->stat->morphs();

            return $morphsRelation->newPivotStatement()
                ->where('stat_id', $this->stat->id)
                ->whereIn(
                    'model_id',
                    $morphs->filter(function (mixed $morph) {
                        return $morph->stats->contains('slug', $this->stat->slug->getValue());
                    })
                    ->pluck('id')
                    ->toArray()
                )
                ->where('model_type', $this->stat->model_type)
                ->increment('value');
        });
    }
}
