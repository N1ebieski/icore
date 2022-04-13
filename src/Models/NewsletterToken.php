<?php

namespace N1ebieski\ICore\Models;

use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\NewsletterToken\NewsletterTokenFactory;

class NewsletterToken extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletters_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'token', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['token', 'email'];

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return NewsletterTokenFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class, 'email', 'email');
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return NewsletterTokenFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
