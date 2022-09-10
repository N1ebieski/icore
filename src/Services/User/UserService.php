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

namespace N1ebieski\ICore\Services\User;

use Throwable;
use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager as DB;

class UserService
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @param Hasher $hasher
     * @param DB $db
     */
    public function __construct(
        protected User $user,
        protected Hasher $hasher,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return User
     * @throws Throwable
     */
    public function create(array $attributes): User
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
     *
     * @param array $attributes
     * @return User
     * @throws Throwable
     */
    public function update(array $attributes): User
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('roles', $attributes)) {
                $this->user->syncRoles(array_merge($attributes['roles'] ?? [], ['user']));
            }

            $this->user->update([
                'name' => $attributes['name'],
                'email' => $attributes['email']
            ]);

            return $this->user;
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
     *
     * @return null|bool
     * @throws Throwable
     */
    public function delete(): ?bool
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
