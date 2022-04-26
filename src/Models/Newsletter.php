<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\NewsletterToken;
use Illuminate\Database\Eloquent\Relations\HasOne;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Newsletter\NewsletterFactory;

/**
 * @property Status $status
 */
class Newsletter extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'status'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['email'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => Status::ACTIVE,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => \N1ebieski\ICore\Casts\Newsletter\StatusCast::class,
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
        return \N1ebieski\ICore\Database\Factories\Newsletter\NewsletterFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function token(): HasOne
    {
        return $this->hasOne(NewsletterToken::class, 'email', 'email');
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return NewsletterFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
