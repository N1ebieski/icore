<?php

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
     * @var string
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
     * @return static
     */
    public function active()
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
     * @return static
     */
    public function marketing()
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
     * @return static
     */
    public function user()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::USER);
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function admin()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::ADMIN);
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function superAdmin()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::SUPER_ADMIN);
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function api()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Name::API);
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function banUser()
    {
        return $this->afterCreating(function (User $user) {
            $user->ban()->create();
        });
    }
}
