<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use N1ebieski\ICore\Cache\BanValue\BanValueCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Repositories\BanValue\BanValueRepo;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory;

/**
 * @property Type $type
 */
class BanValue extends Model
{
    use HasFilterable;
    use HasFullTextSearchable;
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type', 'value'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['value'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bans_values';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\ICore\Casts\BanValue\TypeCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory::new();
    }

    // Scopes

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null): ?Builder
    {
        return $query->when($type !== null, function ($query) use ($type) {
            $query->where('type', $type);
        });
    }

    // Factories

    /**
     * [makeRepo description]
     * @return BanValueRepo [description]
     */
    public function makeRepo()
    {
        return App::make(BanValueRepo::class, ['banvalue' => $this]);
    }

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return BanValueFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
