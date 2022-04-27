<?php

namespace N1ebieski\ICore\Database\Factories\Comment;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $content = $this->faker->text(300);

        return [
            'content_html' => $content,
            'content' => $content,
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
    public function inactive()
    {
        return $this->state(function () {
            return [
                'status' => Status::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withUser()
    {
        return $this->for(User::makeFactory());
    }
}
