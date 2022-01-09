<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken as BasePersonalAccessToken;

class PersonalAccessToken extends BasePersonalAccessToken
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fillable[] = 'expired_at';
        $this->casts['expired_at'] = 'datetime';

        parent::__construct($attributes);
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function symlink(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }
}
