<?php

namespace N1ebieski\ICore\Models\Stat;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Cache\StatCache;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\StatService;
use N1ebieski\ICore\Repositories\StatRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;

class Stat extends Model
{
    use Polymorphic, Carbonable;

    // Configuration

    /**
     * [public description]
     * @var string
     */
    public const CLICK = 'click';

    /**
     * [public description]
     * @var string
     */
    public const VIEW = 'view';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['slug'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Makers

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
