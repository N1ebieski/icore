<?php

namespace N1ebieski\ICore\Database\Factories\Rating;

use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

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
    public function one()
    {
        return $this->state(function () {
            return [
                'rating' => 1
            ];
        });
    }
}
