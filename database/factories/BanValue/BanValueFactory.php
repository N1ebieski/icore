<?php

namespace N1ebieski\ICore\Database\Factories\BanValue;

use N1ebieski\ICore\Models\BanValue;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class BanValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BanValue::class;

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
    public function ip()
    {
        return $this->state(function () {
            return [
                'type' => Type::IP,
                'value' => $this->faker->ipv4
            ];
        });
    }
}
