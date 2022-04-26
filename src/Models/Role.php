<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Services\RoleService;
use N1ebieski\ICore\Repositories\RoleRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Filterable;
use Spatie\Permission\Models\Role as BaseRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Database\Factories\Role\RoleFactory;

class Role extends BaseRole
{
    use Carbonable;
    use Filterable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
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
