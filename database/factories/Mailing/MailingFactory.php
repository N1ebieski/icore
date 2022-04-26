<?php

namespace N1ebieski\ICore\Database\Factories\Mailing;

use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mailing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $content = $this->faker->text(2000);

        return [
            'title' => $this->faker->sentence(5),
            'content_html' => $content,
            'content' => $content,
            'status' => Status::INACTIVE
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
}
