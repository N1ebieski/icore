<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Services\Socialite\SocialiteService;
use N1ebieski\ICore\Repositories\Socialite\SocialiteRepo;
use N1ebieski\ICore\Database\Factories\Socialite\SocialiteFactory;

class Socialite extends Model
{
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id', 'provider_name', 'provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = ['provider_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
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
