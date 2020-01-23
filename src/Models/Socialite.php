<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use N1ebieski\ICore\Repositories\SocialiteRepo;
use N1ebieski\ICore\Services\SocialiteService;

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
        return app()->make(SocialiteRepo::class, ['socialite' => $this]);
    }

    /**
     * [makeService description]
     * @return SocialiteService [description]
     */
    public function makeService()
    {
        return app()->make(SocialiteService::class, ['socialite' => $this]);
    }
}
