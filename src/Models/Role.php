<?php

namespace N1ebieski\ICore\Models;

use DateTime;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\ValueObjects\Role\Name;
use N1ebieski\ICore\Services\Role\RoleService;
use Spatie\Permission\Models\Role as BaseRole;
use N1ebieski\ICore\Repositories\Role\RoleRepo;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Role\RoleFactory;

/**
 * @property int $id
 * @property Name $name
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Role extends BaseRole
{
    use HasCarbonable;
    use HasFilterable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'guard_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'name' => \N1ebieski\ICore\Casts\Role\NameCast::class,
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
        return \N1ebieski\ICore\Database\Factories\Role\RoleFactory::new();
    }

    // Factories

    /**
     * [makeRepo description]
     * @return RoleRepo [description]
     */
    public function makeRepo()
    {
        return App::make(RoleRepo::class, ['role' => $this]);
    }

    /**
     * [makeService description]
     * @return RoleService [description]
     */
    public function makeService()
    {
        return App::make(RoleService::class, ['role' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return RoleFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
