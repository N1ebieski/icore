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

trait HasPolymorphic
{
    // Scopes

    /**
     * [scopePoliType description]
     * @param  Builder $query      [description]
     * @return Builder|null              [description]
     */
    public function scopePoliType(Builder $query): ?Builder
    {
        return $query->when($this->model_type !== null, function ($query) {
            $query->where("{$this->getTable()}.model_type", $this->model_type);
        });
    }

    /**
     * [scopePoli description]
     * @param  Builder $query [description]
     * @return Builder|null        [description]
     */
    public function scopePoli(Builder $query): ?Builder
    {
        return $query->when($this->model_id !== null, function ($query) {
            /**
             * @phpstan-ignore-next-line
             */
            $query->poliType()->where("{$this->getTable()}.model_id", $this->model_id);
        });
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        $type = substr($this->model_type, strrpos($this->model_type, "\\") + 1);

        return strtolower($type);
    }
}
