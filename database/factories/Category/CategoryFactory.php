<?php

namespace N1ebieski\ICore\Database\Factories\Category;

use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word),
            'status' => rand(Category::INACTIVE, Category::ACTIVE)
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
                'status' => Category::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function sentence()
    {
        return $this->state(function () {
            return [
                'name' => ucfirst($this->faker->word . ' ' . $this->faker->word)
            ];
        });
    }
}
