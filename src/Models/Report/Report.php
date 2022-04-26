<?php

namespace N1ebieski\ICore\Models\Report;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\ReportService;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Report\ReportFactory;

class Report extends Model
{
    use Polymorphic;
    use Carbonable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'model_id' => 'integer',
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
        return \N1ebieski\ICore\Database\Factories\Report\ReportFactory::new();
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

    /**
     * [morph description]
     * @return MorphTo [description]
     */
    public function morph(): MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    // Factories

    /**
     * [makeService description]
     * @return ReportService [description]
     */
    public function makeService()
    {
        return App::make(ReportService::class, ['report' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return ReportFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
