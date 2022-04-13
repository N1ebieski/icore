<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Services\SocialiteService;
use N1ebieski\ICore\Repositories\SocialiteRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Socialite\SocialiteFactory;

class Socialite extends Model
{
    use Carbonable;
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SocialiteFactory::new();
    }

    // Relations

    /**
     * [user description]
     * @return BelongsTo [description]
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\User::class);
    }

    // Factories

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

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return SocialiteFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
