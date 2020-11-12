<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Hashing\Hasher;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

class UserService implements
    Creatable,
    Updatable,
    StatusUpdatable,
    Deletable,
    GlobalDeletable
{
    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Hasher
     */
    protected $hasher;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Hasher $hasher
     */
    public function __construct(User $user, Hasher $hasher)
    {
        $this->user = $user;

        $this->hasher = $hasher;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes) : Model
    {
        $this->user->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => isset($attributes['password']) ?
                $this->hasher->make($attributes['password'])
                : null
        ]);

        $this->user->assignRole(array_merge($attributes['roles'] ?? [], ['user']));

        return $this->user;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function update(array $attributes) : bool
    {
        $this->user->syncRoles(array_merge($attributes['roles'] ?? [], ['user']));

        return $this->user->update([
            'name' => $attributes['name'],
            'email' => $attributes['email']
        ]);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function updateStatus(array $attributes) : bool
    {
        return $this->user->update([
            'status' => $attributes['status']
        ]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete() : bool
    {
        $this->user->ban()->delete();

        $this->user->emails()->delete();

        return $this->user->delete();
    }

    /**
     * Undocumented function
     *
     * @param array $ids
     * @return integer
     */
    public function deleteGlobal(array $ids) : int
    {
        $this->user->ban()->make()->whereIn('model_id', $ids)
            ->where('model_type', $this->user->getMorphClass())->delete();

        $this->user->emails()->make()->whereIn('model_id', $ids)
            ->where('model_type', $this->user->getMorphClass())->delete();

        return $this->user->whereIn('id', $ids)->delete();
    }
}
