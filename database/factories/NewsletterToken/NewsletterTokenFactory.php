<?php

namespace N1ebieski\ICore\Database\Factories\NewsletterToken;

use Illuminate\Support\Str;
use N1ebieski\ICore\Models\NewsletterToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsletterToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'token' => Str::random(30)
        ];
    }
}
