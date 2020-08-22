<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\SocialiteService;
use N1ebieski\ICore\Repositories\SocialiteRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * [Socialite description]
 */
class Socialite extends Model
{
    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'provider_name', 'provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['provider_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * [user description]
     * @return BelongsTo [description]
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    // Makers

    /**
     * [makeRepo description]
     * @return SocialiteRepo [description]
     */
    public function makeRepo()
    {
        return App::make(SocialiteRepo::class, ['socialite' => $this]);
    }

    /**
     * [makeService description]
     * @return SocialiteService [description]
     */
    public function makeService()
    {
        return App::make(SocialiteService::class, ['socialite' => $this]);
    }
}
