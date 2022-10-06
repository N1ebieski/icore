<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
 * N1ebieski\ICore\Models\Role
 *
 * @property int $id
 * @property Name $name
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $guard_name
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \N1ebieski\ICore\Database\Factories\Role\RoleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterExcept(?array $except = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterOrderBy(?string $orderby = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterReport(?int $report = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterSearch(?string $search = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role filterStatus(?int $status = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
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
