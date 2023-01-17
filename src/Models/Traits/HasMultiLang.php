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
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * @mixin Model
 */
trait HasMultiLang
{
    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function langs(): HasMany
    {
        $className = class_basename($this::class);

        return $this->hasMany('N1ebieski\\ICore\\Models\\' . $className . 'Lang\\' . $className . 'Lang');
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function currentLang(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\CurrentLang::class, [
            'model' => $this
        ])();
    }

    // Checkers

    public function hasAdditionalLangs(): bool
    {
        return count(Config::get('icore.multi_langs')) > 1 && $this->langs->count() > 1;
    }

    // Scopes

    /**
     *
     * @param Builder $query
     * @return Builder
     * @throws MassAssignmentException
     */
    public function scopeMultiLang(Builder $query)
    {
        /** @var Model */
        $lang = $this->langs()->make();

        return $query->join($lang->getTable(), function (JoinClause $join) use ($lang) {
            return $join->on("{$this->getTable()}.{$this->getKeyName()}", '=', "{$lang->getTable()}.category_id")
                ->where("{$lang->getTable()}.lang", Config::get('app.locale'));
        })
        ->with('langs');
    }

    /**
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLang(Builder $query)
    {
        /** @var Model */
        $lang = $this->langs()->make();

        return $query->where("{$lang->getTable()}.lang", Config::get('app.locale'));
    }
}
