<?php

namespace N1ebieski\ICore\Database\Factories\Link;

use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\ValueObjects\Link\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'name' => $this->faker->sentence(2)
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function link()
    {
        return $this->state(function () {
            return [
                'type' => Type::LINK,
            ];
        });
    }
}
