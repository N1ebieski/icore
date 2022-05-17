<?php

namespace N1ebieski\ICore\Services\User;

use Throwable;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use N1ebieski\ICore\Services\Interfaces\GlobalDeleteInterface;
use N1ebieski\ICore\Services\Interfaces\StatusUpdateInterface;

class UserService implements
    CreateInterface,
    UpdateInterface,
    StatusUpdateInterface,
    DeleteInterface,
    GlobalDeleteInterface
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
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Hasher $hasher
     * @param DB $db
     */
    public function __construct(User $user, Hasher $hasher, DB $db)
    {
        $this->user = $user;

        $this->hasher = $hasher;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $user = $this->user->create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => isset($attributes['password']) ?
                    $this->hasher->make($attributes['password'])
                    : null
            ]);

            if (array_key_exists('roles', $attributes)) {
                $user->assignRole(array_merge($attributes['roles'] ?? [], ['user']));
            }

            return $user;
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('roles', $attributes)) {
                $this->user->syncRoles(array_merge($attributes['roles'] ?? [], ['user']));
            }

            return $this->user->update([
                'name' => $attributes['name'],
                'email' => $attributes['email']
            ]);
        });
    }

    /**
     *
     * @param int $status
     * @return bool
     * @throws Throwable
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            return $this->user->update(['status' => $status]);
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            $this->user->ban()->delete();

            $this->user->emails()->delete();

            $this->user->tokens()->delete();

            return $this->user->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @param array $ids
     * @return integer
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            $this->user->ban()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->user->getMorphClass())->delete();

            $this->user->emails()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->user->getMorphClass())->delete();

            $this->user->tokens()->make()->whereIn('tokenable_id', $ids)
                ->where('tokenable_type', $this->user->getMorphClass())->delete();

            return $this->user->whereIn('id', $ids)->delete();
        });
    }
}
