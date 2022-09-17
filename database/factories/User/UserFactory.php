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

namespace N1ebieski\ICore\Database\Factories\User;

use Carbon\Carbon;
use Illuminate\Support\Str;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\ValueObjects\Role\Name;
use N1ebieski\ICore\ValueObjects\User\Status;
use N1ebieski\ICore\ValueObjects\User\Marketing;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<User>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => str_replace("'", '', $this->faker->unique()->name),
            'ip' => $this->faker->ipv4,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => Carbon::now(),
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
            'remember_token' => Str::random(10),
            'status' => rand(Status::INACTIVE, Status::ACTIVE)
        ];
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function active(): self
    {
        return $this->state(function () {
            return [
                'status' => Status::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function marketing(): self
    {
        return $this->state(function () {
            return [
                'marketing' => Marketing::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function user(): self
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::USER);
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function admin(): self
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::ADMIN);
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function superAdmin(): self
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::SUPER_ADMIN);
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function api(): self
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::API);
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function banUser(): self
    {
        return $this->afterCreating(function (User $user) {
            $user->ban()->create();
        });
    }
}
