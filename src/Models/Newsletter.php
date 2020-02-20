<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * [Newsletter description]
 */
class Newsletter extends Model
{
    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'token', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'email'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 0,
    ];

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('status', 1);
    }
}
