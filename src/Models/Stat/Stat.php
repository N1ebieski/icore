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

namespace N1ebieski\ICore\Models\Stat;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Cache\Stat\StatCache;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use N1ebieski\ICore\Services\Stat\StatService;
use N1ebieski\ICore\Repositories\Stat\StatRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 * N1ebieski\ICore\Models\Stat\Stat
 *
 * @property Slug $slug
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read string $model_type
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection $morphs
 * @method static \Illuminate\Database\Eloquent\Relations\MorphToMany|Builder morphs()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat poli()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stat extends Model
{
    use HasPolymorphic;
    use HasCarbonable;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['slug'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'slug' => \N1ebieski\ICore\Casts\Stat\SlugCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Factories

    /**
     * [makeRepo description]
     * @return StatRepo [description]
     */
    public function makeRepo()
    {
        return App::make(StatRepo::class, ['stat' => $this]);
    }

    /**
     * [makeCache description]
     * @return StatCache [description]
     */
    public function makeCache()
    {
        return App::make(StatCache::class, ['stat' => $this]);
    }

    /**
     * [makeService description]
     * @return StatService [description]
     */
    public function makeService()
    {
        return App::make(StatService::class, ['stat' => $this]);
    }
}
