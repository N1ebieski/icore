<?php

namespace N1ebieski\ICore\Database\Factories\MailingEmail;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailingEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MailingEmail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withUser()
    {
        return $this->for(User::factory());
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function email()
    {
        return $this->state(function () {
            return [
                'email' => $this->faker->unique()->safeEmail
            ];
        });
    }
}
