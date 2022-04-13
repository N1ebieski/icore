<?php

namespace N1ebieski\ICore\Database\Factories\Socialite;

use Illuminate\Support\Str;
use N1ebieski\ICore\Models\Socialite;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Socialite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'provider_name' => $this->faker->randomElement(['facebook', 'twitter']),
            'provider_id' => Str::random(8)
        ];
    }
}
