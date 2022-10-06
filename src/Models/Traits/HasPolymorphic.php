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

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Container\BindingResolutionException;

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
            // @phpstan-ignore-next-line
            $query->poliType()->where("{$this->getTable()}.model_id", $this->model_id);
        });
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function poli(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\Poli::class, ['model' => $this])();
    }
}
